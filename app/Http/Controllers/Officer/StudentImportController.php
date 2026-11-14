<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentImportController extends Controller
{
    // A file may have at most this many students (each one needs a slow password hash).
    private const MAX_ROWS = 100;

    // How many problems the report shows at most.
    private const MAX_REPORTED = 50;

    // These columns must be in the header row.
    private const REQUIRED_COLUMNS = [
        'first_name', 'last_name', 'email', 'student_no', 'sex', 'year_level', 'program_code',
    ];

    // Show the upload page.
    public function create()
    {
        Gate::authorize('manage-ssg');

        return view('officer.students.import', ['maxRows' => self::MAX_ROWS]);
    }

    // Check the file, then import everything or nothing.
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'], // 2 MB
        ]);

        // Hashing many passwords takes a while: allow up to 2 minutes.
        set_time_limit(120);

        // Phase 1: read the file.
        $parsed = $this->readCsv($request->file('file')->getRealPath());

        if (isset($parsed['fatal'])) {
            return back()->with('import_fatal', $parsed['fatal']);
        }

        // Phase 2: check every row. Nothing is saved yet.
        [$prepared, $errors] = $this->checkRows($parsed['rows']);

        if (count($errors) > 0) {
            return back()
                ->with('import_errors', array_slice($errors, 0, self::MAX_REPORTED))
                ->with('import_error_count', count($errors))
                ->with('import_total', count($parsed['rows']));
        }

        // Phase 3: save everything in ONE transaction. If anything fails, nothing is kept.
        $studentRole = Role::where('name', 'Student')->firstOrFail();

        DB::transaction(function () use ($prepared, $studentRole) {
            foreach ($prepared as $row) {
                $user = User::create([
                    'first_name'  => $row['first_name'],
                    'middle_name' => $row['middle_name'],
                    'last_name'   => $row['last_name'],
                    'email'       => $row['email'],
                    // Temporary password: the student number, hashed.
                    'password'    => Hash::make($row['student_no']),
                    'role_id'     => $studentRole->id,
                    'is_active'   => true,
                    'is_admin'    => false,
                    // They must choose their own password at first login.
                    'must_change_password' => true,
                ]);

                Student::create([
                    'user_id'    => $user->id,
                    'program_id' => $row['program_id'],
                    'student_no' => $row['student_no'],
                    'sex'        => $row['sex'],
                    'year_level' => (int) $row['year_level'],
                ]);
            }
        });

        $count = count($prepared);

        DashboardCache::people();

        AuditLog::record(
            'students.imported',
            "Imported {$count} student(s) from the file \"" . $request->file('file')->getClientOriginalName() . '".'
        );

        return redirect()->route('officer.students.index')
            ->with('success', "{$count} student(s) imported. Their temporary password is their student number.");
    }

    // Read the CSV into rows. Returns ['rows' => [...]] or ['fatal' => 'message'].
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return ['fatal' => 'The file could not be opened.'];
        }

        // Pick the separator from the first line (Excel in some countries uses ";").
        $firstLine = (string) fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $header = fgetcsv($handle, 0, $delimiter);

        if (! $header || count(array_filter($header, fn ($cell) => trim((string) $cell) !== '')) === 0) {
            fclose($handle);

            return ['fatal' => 'The file is empty, or its first line is not a header row.'];
        }

        // Remove the invisible BOM mark that Excel puts at the start of a CSV.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);

        // "First Name" and "first-name" both become "first_name".
        $columns = array_map(
            fn ($name) => Str::of((string) $name)->trim()->lower()->replace([' ', '-'], '_')->toString(),
            $header
        );

        $missing = array_diff(self::REQUIRED_COLUMNS, $columns);

        if (count($missing) > 0) {
            fclose($handle);

            return ['fatal' => 'The header row is missing these columns: ' . implode(', ', $missing) . '.'];
        }

        $rows       = [];
        $lineNumber = 1; // the header is line 1

        while (($cells = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;

            // Skip blank lines.
            if (count(array_filter($cells, fn ($cell) => trim((string) $cell) !== '')) === 0) {
                continue;
            }

            $row = ['line' => $lineNumber];

            foreach ($columns as $index => $name) {
                $row[$name] = trim((string) ($cells[$index] ?? ''));
            }

            $rows[] = $row;

            if (count($rows) > self::MAX_ROWS) {
                fclose($handle);

                return ['fatal' => 'The file has more than ' . self::MAX_ROWS . ' students. Please split it into smaller files.'];
            }
        }

        fclose($handle);

        if (count($rows) === 0) {
            return ['fatal' => 'The file has a header row but no students.'];
        }

        return ['rows' => $rows];
    }

    // Validate every row. Returns [rows ready to save, list of problems].
    private function checkRows(array $rows): array
    {
        $programs = Program::all()->keyBy(fn ($program) => Str::lower($program->code));

        $prepared    = [];
        $errors      = [];
        $seenEmails  = [];
        $seenNumbers = [];

        foreach ($rows as $row) {
            $data = [
                'first_name'   => $row['first_name'] ?? '',
                'middle_name'  => ($row['middle_name'] ?? '') !== '' ? $row['middle_name'] : null,
                'last_name'    => $row['last_name'] ?? '',
                'email'        => $row['email'] ?? '',
                'student_no'   => $row['student_no'] ?? '',
                'sex'          => Str::ucfirst(Str::lower($row['sex'] ?? '')), // "male" becomes "Male"
                'year_level'   => $row['year_level'] ?? '',
                'program_code' => $row['program_code'] ?? '',
            ];

            $validator = Validator::make($data, [
                'first_name'   => ['required', 'string', 'max:255'],
                'middle_name'  => ['nullable', 'string', 'max:255'],
                'last_name'    => ['required', 'string', 'max:255'],
                'email'        => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
                'student_no'   => ['required', 'string', 'max:50', 'unique:students,student_no'],
                'sex'          => ['required', Rule::in(['Male', 'Female'])],
                'year_level'   => ['required', 'integer', 'between:1,6'],
                'program_code' => ['required', 'string'],
            ], [], [
                'first_name'   => 'first name',
                'last_name'    => 'last name',
                'student_no'   => 'student number',
                'year_level'   => 'year level',
                'program_code' => 'program code',
            ]);

            $messages = $validator->fails() ? $validator->errors()->all() : [];

            // The program code must match a program we have (capitals do not matter).
            $program = $programs[Str::lower($data['program_code'])] ?? null;

            if ($data['program_code'] !== '' && ! $program) {
                $messages[] = 'The program code "' . $data['program_code'] . '" does not exist.';
            }

            // The same email or student number twice inside the file.
            $emailKey  = Str::lower($data['email']);
            $numberKey = Str::lower($data['student_no']);

            if ($emailKey !== '') {
                if (isset($seenEmails[$emailKey])) {
                    $messages[] = 'The email is repeated in this file (also on line ' . $seenEmails[$emailKey] . ').';
                } else {
                    $seenEmails[$emailKey] = $row['line'];
                }
            }

            if ($numberKey !== '') {
                if (isset($seenNumbers[$numberKey])) {
                    $messages[] = 'The student number is repeated in this file (also on line ' . $seenNumbers[$numberKey] . ').';
                } else {
                    $seenNumbers[$numberKey] = $row['line'];
                }
            }

            if (count($messages) > 0) {
                $errors[] = [
                    'line'     => $row['line'],
                    'who'      => Str::squish($data['first_name'] . ' ' . $data['last_name']) ?: ($data['email'] ?: '(empty row)'),
                    'messages' => $messages,
                ];

                continue;
            }

            $prepared[] = $data + ['program_id' => $program->id];
        }

        return [$prepared, $errors];
    }
}

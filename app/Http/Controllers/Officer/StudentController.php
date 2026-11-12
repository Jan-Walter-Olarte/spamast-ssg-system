<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Support\DashboardCache;

class StudentController extends Controller
{
    // List students with search, institute and program filters, 15 per page.
    public function index(Request $request)
    {
        $search      = trim((string) $request->query('search'));
        $instituteId = $request->query('institute');
        $programId   = $request->query('program');

        $students = Student::with(['user', 'program.institute'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_no', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($instituteId, function ($query) use ($instituteId) {
                $query->whereHas('program', fn ($p) => $p->where('institute_id', $instituteId));
            })
            ->when($programId, fn ($query) => $query->where('program_id', $programId))
            ->orderBy('student_no')
            ->paginate(15)
            ->withQueryString();

        return view('officer.students.index', [
            'students'    => $students,
            'institutes'  => Institute::orderBy('code')->get(),
            'programs'    => Program::orderBy('code')->get(),
            'search'      => $search,
            'instituteId' => $instituteId,
            'programId'   => $programId,
        ]);
    }

    // Show the empty create form.
    public function create()
    {
        return view('officer.students.create', [
            'institutes' => Institute::orderBy('code')->get(),
            'programs'   => Program::orderBy('code')->get(),
        ]);
    }

    // Validate, then save the user and the student together.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'student_no'  => ['required', 'string', 'max:50', 'unique:students,student_no'],
            'sex'         => ['required', Rule::in(['Male', 'Female'])],
            'year_level'  => ['required', 'integer', 'between:1,6'],
            'program_id'  => ['required', 'exists:programs,id'],
        ]);

        // One transaction: both rows are saved, or neither is.
        DB::transaction(function () use ($data) {
            $studentRole = Role::where('name', 'Student')->firstOrFail();

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name'  => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name'   => $data['last_name'],
                    // Temporary password: the student number, hashed.
                    'password'    => Hash::make($data['student_no']),
                    'role_id'     => $studentRole->id,
                    'is_active'   => true,
                    'is_admin'    => false,
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'program_id' => $data['program_id'],
                    'student_no' => $data['student_no'],
                    'sex'        => $data['sex'],
                    'year_level' => $data['year_level'],
                ]
            );
        });

        DashboardCache::people();

        return redirect()->route('officer.students.index')
            ->with('success', 'Student added. Their temporary password is their student number.');
    }

    // Show the edit form.
    public function edit(Student $student)
    {
        return view('officer.students.edit', [
            'student'    => $student->load('user'),
            'institutes' => Institute::orderBy('code')->get(),
            'programs'   => Program::orderBy('code')->get(),
        ]);
    }

    // Update the user and the student together.
    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            // ignore(...): the student's own email and number are not duplicates of themselves.
            'email'       => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'student_no'  => ['required', 'string', 'max:50', Rule::unique('students', 'student_no')->ignore($student->id)],
            'sex'         => ['required', Rule::in(['Male', 'Female'])],
            'year_level'  => ['required', 'integer', 'between:1,6'],
            'program_id'  => ['required', 'exists:programs,id'],
        ]);

        DB::transaction(function () use ($data, $student) {
            $student->user->update([
                'first_name'  => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name'   => $data['last_name'],
                'email'       => $data['email'],
            ]);

            $student->update([
                'program_id' => $data['program_id'],
                'student_no' => $data['student_no'],
                'sex'        => $data['sex'],
                'year_level' => $data['year_level'],
            ]);
        });

        return redirect()->route('officer.students.index')
            ->with('success', 'Student updated successfully.');
    }

    // Delete the student's user account; the students row goes with it (cascade).
    public function destroy(Student $student): RedirectResponse
    {
        $student->user->delete();

        DashboardCache::people();

        return redirect()->route('officer.students.index')
            ->with('success', 'Student deleted.');
    }
}

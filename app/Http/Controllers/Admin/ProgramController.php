<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\DashboardCache;

class ProgramController extends Controller
{
    // List programs with search, institute filter and pagination.
    public function index(Request $request)
    {
        $search      = trim((string) $request->query('search'));
        $instituteId = $request->query('institute');

        $programs = DB::table('programs')
            // join: attach each program's institute row.
            ->join('institutes', 'institutes.id', '=', 'programs.institute_id')
            ->select('programs.*', 'institutes.code as institute_code')
            // Sub-query: how many students each program has.
            ->selectSub(
                DB::table('students')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('students.program_id', 'programs.id'),
                'students_count'
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('programs.code', 'like', "%{$search}%")
                      ->orWhere('programs.name', 'like', "%{$search}%")
                      ->orWhere('institutes.code', 'like', "%{$search}%");
                });
            })
            ->when($instituteId, fn ($query) => $query->where('programs.institute_id', $instituteId))
            ->orderBy('institutes.code')
            ->orderBy('programs.code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.programs.index', [
            'programs'    => $programs,
            'institutes'  => $this->institutes(),
            'search'      => $search,
            'instituteId' => $instituteId,
        ]);
    }

    // Show the empty create form.
    public function create()
    {
        return view('admin.programs.create', [
            'institutes' => $this->institutes(),
        ]);
    }

    // Validate and save a new program.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'code'         => ['required', 'string', 'max:50', 'unique:programs,code'],
            'name'         => ['required', 'string', 'max:255'],
        ]);

        // Query Builder does not set the timestamps, so we do.
        DB::table('programs')->insert([
            'institute_id' => $data['institute_id'],
            'code'         => $data['code'],
            'name'         => $data['name'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DashboardCache::admin();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program added successfully.');
    }

    // Show the edit form.
    public function edit(string $program)
    {
        return view('admin.programs.edit', [
            'program'    => $this->findOrFail($program),
            'institutes' => $this->institutes(),
        ]);
    }

    // Validate and save changes.
    public function update(Request $request, string $program): RedirectResponse
    {
        $row = $this->findOrFail($program);

        $data = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            // ignore(...): the program's own code is not a duplicate of itself.
            'code'         => ['required', 'string', 'max:50', Rule::unique('programs', 'code')->ignore($row->id)],
            'name'         => ['required', 'string', 'max:255'],
        ]);

        DB::table('programs')
            ->where('id', $row->id)
            ->update([
                'institute_id' => $data['institute_id'],
                'code'         => $data['code'],
                'name'         => $data['name'],
                'updated_at'   => now(),
            ]);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    // Delete a program, unless it still has students.
    public function destroy(string $program): RedirectResponse
    {
        $row = $this->findOrFail($program);

        if (DB::table('students')->where('program_id', $row->id)->exists()) {
            return redirect()->route('admin.programs.index')
                ->with('error', 'Cannot delete this program because it still has students. Move or remove its students first.');
        }

        DB::table('programs')->where('id', $row->id)->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted.');
    }

    // All institutes, for the dropdowns.
    private function institutes()
    {
        return DB::table('institutes')->orderBy('code')->get();
    }

    // Find one row or stop with a 404 page.
    private function findOrFail(string $id): object
    {
        return DB::table('programs')->where('id', $id)->first() ?? abort(404);
    }
}

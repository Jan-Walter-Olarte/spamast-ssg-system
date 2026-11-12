<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    // List institutes with search and pagination.
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $institutes = DB::table('institutes')
            ->select('institutes.*')
            // A small sub-query: how many programs each institute has.
            ->selectSub(
                DB::table('programs')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('programs.institute_id', 'institutes.id'),
                'programs_count'
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('institutes.code', 'like', "%{$search}%")
                      ->orWhere('institutes.name', 'like', "%{$search}%");
                });
            })
            ->orderBy('institutes.code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.institutes.index', [
            'institutes' => $institutes,
            'search'     => $search,
        ]);
    }

    // Show the empty create form.
    public function create()
    {
        return view('admin.institutes.create');
    }

    // Validate and save a new institute.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:institutes,code'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Query Builder does not set the timestamps, so we do.
        DB::table('institutes')->insert([
            'code'       => $data['code'],
            'name'       => $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.institutes.index')
            ->with('success', 'Institute added successfully.');
    }

    // Show the edit form.
    public function edit(string $institute)
    {
        return view('admin.institutes.edit', [
            'institute' => $this->findOrFail($institute),
        ]);
    }

    // Validate and save changes.
    public function update(Request $request, string $institute): RedirectResponse
    {
        $row = $this->findOrFail($institute);

        $data = $request->validate([
            // ignore(...): the institute's own code is not a duplicate of itself.
            'code' => ['required', 'string', 'max:50', Rule::unique('institutes', 'code')->ignore($row->id)],
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::table('institutes')
            ->where('id', $row->id)
            ->update([
                'code'       => $data['code'],
                'name'       => $data['name'],
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.institutes.index')
            ->with('success', 'Institute updated successfully.');
    }

    // Delete an institute, unless it still has programs.
    public function destroy(string $institute): RedirectResponse
    {
        $row = $this->findOrFail($institute);

        if (DB::table('programs')->where('institute_id', $row->id)->exists()) {
            return redirect()->route('admin.institutes.index')
                ->with('error', 'Cannot delete this institute because it still has programs. Delete or move its programs first.');
        }

        DB::table('institutes')->where('id', $row->id)->delete();

        return redirect()->route('admin.institutes.index')
            ->with('success', 'Institute deleted.');
    }

    // Find one row or stop with a 404 page.
    private function findOrFail(string $id): object
    {
        return DB::table('institutes')->where('id', $id)->first() ?? abort(404);
    }
}

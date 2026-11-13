<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    // Show the empty create form.
    public function create()
    {
        return view('officer.announcements.create');
    }

    // Validate and save a new announcement.
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $data = $request->validate($this->rules());

        // Store the optional attachment in storage/app/public/announcements.
        $path = $request->hasFile('attachment')
            ? $this->storeAttachment($request, $data['title'])
            : null;

        Announcement::create([
            'user_id'    => $request->user()->id, // the logged-in Officer, never from the form
            'title'      => $data['title'],
            'body'       => $data['body'],
            'attachment' => $path,
        ]);

        DashboardCache::officer();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement posted.');
    }

    // Show the edit form.
    public function edit(Announcement $announcement)
    {
        Gate::authorize('manage-ssg');

        return view('officer.announcements.edit', ['announcement' => $announcement]);
    }

    // Validate and save changes.
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $data = $request->validate($this->rules());

        $announcement->title = $data['title'];
        $announcement->body  = $data['body'];

        // A new file replaces the old one: delete the old file first.
        if ($request->hasFile('attachment')) {
            if ($announcement->attachment) {
                Storage::disk('public')->delete($announcement->attachment);
            }

            $announcement->attachment = $this->storeAttachment($request, $data['title']);
        }

        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated.');
    }

    // Delete an announcement and its file.
    public function destroy(Announcement $announcement): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        if ($announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
        }

        $announcement->delete();

        DashboardCache::officer();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    // Save the uploaded file as "<title-slug>-<random>.<extension>" and return its path.
    private function storeAttachment(Request $request, string $title): string
    {
        $file = $request->file('attachment');

        // slug(): "Midterm Exam Schedule!" becomes "midterm-exam-schedule".
        // The slug is cut to 50 characters, and a short random value keeps every name unique.
        $slug = Str::limit(Str::slug($title), 50, '') ?: 'announcement';
        $name = $slug . '-' . Str::lower(Str::random(6)) . '.' . $file->extension();

        return $file->storeAs('announcements', $name, 'public');
    }

    // The validation rules shared by store() and update().
    private function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'body'       => ['required', 'string'],
            // Optional file: PDF or image, at most 5 MB (5120 kilobytes).
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

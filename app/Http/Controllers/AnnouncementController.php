<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    // List announcements, newest first, 10 per page (Officers, Advisers and Students).
    public function index()
    {
        $announcements = Announcement::with('user')
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);

        return view('announcements.index', ['announcements' => $announcements]);
    }

    // Show one announcement.
    public function show(Announcement $announcement)
    {
        $announcement->load('user');

        return view('announcements.show', ['announcement' => $announcement]);
    }
}

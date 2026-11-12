<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // List events for Officers, Advisers and Students (newest first).
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $events = Event::withCount('attendances')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('venue', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('event_date')
            ->paginate(10)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'search' => $search,
        ]);
    }

    // Show one event with its sessions.
    public function show(Request $request, Event $event)
    {
        $event->load(['sessions.event', 'creator']);

        $user      = $request->user();
        $isStudent = $user->hasRole('Student');
        $student   = $isStudent ? $user->student : null;

        // This student's own attendance rows for this event, indexed by session id.
        $myAttendances = collect();

        if ($student) {
            $myAttendances = Attendance::where('student_id', $student->id)
                ->whereIn('event_session_id', $event->sessions->pluck('id'))
                ->get()
                ->keyBy('event_session_id');
        }

        return view('events.show', [
            'event'         => $event,
            'isStudent'     => $isStudent,
            'student'       => $student,
            'myAttendances' => $myAttendances,
        ]);
    }
}

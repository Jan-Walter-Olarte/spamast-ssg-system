<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // List events for Officers, Advisers and Students, with Upcoming / Past tabs and a date search.
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        // Tab: 'upcoming' (default) or 'past'.
        $tab = $request->query('tab') === 'past' ? 'past' : 'upcoming';

        // Date search: only accept a YYYY-MM-DD value.
        $date = $request->query('date');
        $date = (is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) ? $date : null;

        $events = Event::withCount('attendances')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('venue', 'like', "%{$search}%");
                });
            })
            // A chosen date wins over the tabs.
            ->when($date, fn ($query) => $query->whereDate('event_date', $date))
            ->when(! $date && $tab === 'upcoming', fn ($query) => $query->whereDate('event_date', '>=', today()))
            ->when(! $date && $tab === 'past', fn ($query) => $query->whereDate('event_date', '<', today()))
            // Upcoming: soonest first. Everything else: newest first.
            ->when(! $date && $tab === 'upcoming',
                fn ($query) => $query->orderBy('event_date'),
                fn ($query) => $query->orderByDesc('event_date'))
            ->paginate(10)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'search' => $search,
            'tab'    => $tab,
            'date'   => $date,
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

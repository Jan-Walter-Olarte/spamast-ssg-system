<?php

namespace App\Http\Controllers;

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
    public function show(Event $event)
    {
        $event->load(['sessions', 'creator']);

        return view('events.show', ['event' => $event]);
    }
}

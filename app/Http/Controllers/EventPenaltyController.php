<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Institute;
use App\Models\Penalty;
use App\Models\Program;
use Illuminate\Http\Request;

class EventPenaltyController extends Controller
{
    // List the penalties of one event (Officers and Advisers).
    public function index(Request $request, Event $event)
    {
        // Only people who may view reports. The route already checks the role; this is a second guard.
        abort_unless($request->user()->can('view-reports'), 403);

        $instituteId = $request->query('institute');
        $programId   = $request->query('program');
        $status      = $request->query('status');

        $query = Penalty::with(['student.user', 'student.program.institute'])
            ->where('event_id', $event->id)
            // filled() is true only when the value exists and is not empty.
            ->when($request->filled('institute'), function ($query) use ($instituteId) {
                $query->whereHas('student.program', fn ($p) => $p->where('institute_id', $instituteId));
            })
            ->when($request->filled('program'), function ($query) use ($programId) {
                $query->whereHas('student', fn ($s) => $s->where('program_id', $programId));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $status));

        // Total of the amounts that match the filters (taken before pagination).
        $total = (clone $query)->sum('amount');

        $penalties = $query
            ->orderByDesc('amount')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('penalties.index', [
            'event'       => $event->loadCount('sessions'),
            'penalties'   => $penalties,
            'total'       => $total,
            'institutes'  => Institute::orderBy('code')->get(),
            'programs'    => Program::orderBy('code')->get(),
            'instituteId' => $instituteId,
            'programId'   => $programId,
            'status'      => $status,
        ]);
    }
}

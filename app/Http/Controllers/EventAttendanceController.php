<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Institute;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventAttendanceController extends Controller
{
    // List the attendance records of one event (Officers and Advisers).
    public function index(Request $request, Event $event)
    {
        $event->load('sessions');

        $sessionId   = $request->query('session');
        $instituteId = $request->query('institute');
        $programId   = $request->query('program');

        $attendances = Attendance::with(['student.user', 'student.program.institute', 'session'])
            // Only records that belong to this event's sessions.
            ->whereIn('event_session_id', $event->sessions->pluck('id'))
            ->when($sessionId, fn ($query) => $query->where('event_session_id', $sessionId))
            ->when($instituteId, function ($query) use ($instituteId) {
                $query->whereHas('student.program', fn ($p) => $p->where('institute_id', $instituteId));
            })
            ->when($programId, function ($query) use ($programId) {
                $query->whereHas('student', fn ($s) => $s->where('program_id', $programId));
            })
            ->orderBy('event_session_id')
            ->orderBy('check_in_at')
            ->paginate(20)
            ->withQueryString();

        return view('attendance.index', [
            'event'       => $event,
            'attendances' => $attendances,
            'institutes'  => Institute::orderBy('code')->get(),
            'programs'    => Program::orderBy('code')->get(),
            // Only Officers need the full student list (for the "add a record" form).
            'students'    => Gate::allows('manage-ssg')
                                ? Student::with('user')->orderBy('student_no')->get()
                                : collect(),
            'sessionId'   => $sessionId,
            'instituteId' => $instituteId,
            'programId'   => $programId,
        ]);
    }
}

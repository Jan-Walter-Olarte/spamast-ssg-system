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
        // "Clear filters": forget the saved filters, then show the page with none.
        if ($request->boolean('clear')) {
            $request->session()->forget(['filters.institute_id', 'filters.program_id']);

            return redirect()->route('attendance.index', $event);
        }

        $event->load('sessions');

        // Use the filter from the address when the form was submitted,
        // otherwise fall back to the one saved in the session.
        $instituteId = $request->has('institute')
            ? $request->query('institute')
            : session('filters.institute_id');

        $programId = $request->has('program')
            ? $request->query('program')
            : session('filters.program_id');

        // Save the current choice so the user returns to the same view.
        session([
            'filters.institute_id' => $instituteId,
            'filters.program_id'   => $programId,
        ]);

        // True when the filters came from the session, not from this request.
        $restored = ! $request->has('institute')
            && ! $request->has('program')
            && ($instituteId || $programId);

        $sessionId = $request->query('session');

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
            'restored'    => $restored,
        ]);
    }
}

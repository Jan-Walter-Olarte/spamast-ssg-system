<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\EventSession;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    // A student checks in to one session.
    public function checkIn(Request $request, EventSession $session): RedirectResponse
    {
        // The student record of the logged-in user (never taken from the form).
        $student = $request->user()->student;
        abort_unless($student, 403, 'Your account has no linked student record.');

        // Stop unless check-in is open right now.
        $session->load('event');
        abort_unless($session->isCheckInOpen(), 403, 'Check-in is closed for this session.');

        // The student must type the code shown at the venue.
        if (! $this->codeIsValid($request, $session)) {
            return $this->backToEvent($session)
                ->with('error', 'The code is missing or wrong. Ask the Officer for the code shown at the venue.');
        }

        // One row per student per session: find it, or create it with the check-in time.
        $attendance = Attendance::firstOrCreate(
            ['event_session_id' => $session->id, 'student_id' => $student->id],
            ['check_in_at' => now()]
        );

        if (! $attendance->wasRecentlyCreated) {
            return $this->backToEvent($session)->with('error', 'You have already checked in to this session.');
        }

        DashboardCache::adviser();
        DashboardCache::student($request->user()->id);

        return $this->backToEvent($session)
            ->with('success', 'Checked in at ' . $attendance->check_in_at->format('h:i A') . '.');
    }

    // A student checks out of one session.
    public function checkOut(Request $request, EventSession $session): RedirectResponse
    {
        $student = $request->user()->student;
        abort_unless($student, 403, 'Your account has no linked student record.');

        $session->load('event');
        abort_unless($session->isCheckOutOpen(), 403, 'Check-out is closed for this session.');

        // The same code is needed to check out.
        if (! $this->codeIsValid($request, $session)) {
            return $this->backToEvent($session)
                ->with('error', 'The code is missing or wrong. Ask the Officer for the code shown at the venue.');
        }

        $mine = Attendance::where('event_session_id', $session->id)
            ->where('student_id', $student->id);

        // Already checked out?
        if ((clone $mine)->whereNotNull('check_out_at')->exists()) {
            return $this->backToEvent($session)->with('error', 'You have already checked out of this session.');
        }

        // The row with no check-out yet. No row means no check-in, so this fails with a 404.
        $attendance = $mine->whereNull('check_out_at')->firstOrFail();

        $attendance->update(['check_out_at' => now()]);

        return $this->backToEvent($session)
            ->with('success', 'Checked out at ' . $attendance->check_out_at->format('h:i A') . '.');
    }

    // Does the typed code match this session's code? Case and spaces are ignored.
    private function codeIsValid(Request $request, EventSession $session): bool
    {
        $typed = Str::upper(str_replace(' ', '', (string) $request->input('code')));

        return filled($session->scan_code) && hash_equals($session->scan_code, $typed);
    }

    // Always return to the event page this session belongs to.
    private function backToEvent(EventSession $session): RedirectResponse
    {
        return redirect()->route('events.show', $session->event_id);
    }
}

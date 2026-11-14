<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Event;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    // Add a missing record for a student.
    public function store(Request $request, Event $event): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $data = $request->validate([
            'event_session_id' => [
                'required',
                // The session must belong to THIS event.
                Rule::exists('event_sessions', 'id')->where('event_id', $event->id),
            ],
            'student_id' => ['required', 'exists:students,id'],
        ] + $this->timeRules($request));

        // One row per student per session.
        $exists = Attendance::where('event_session_id', $data['event_session_id'])
            ->where('student_id', $data['student_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'This student already has a record for that session. Edit the existing record instead.');
        }

        $attendance = Attendance::create([
            'event_session_id' => $data['event_session_id'],
            'student_id'       => $data['student_id'],
            'check_in_at'      => $data['check_in_at'] ?? null,
            'check_out_at'     => $data['check_out_at'] ?? null,
        ]);

        DashboardCache::adviser();

        AuditLog::record(
            'attendance.added',
            'Added attendance for ' . $this->describe($attendance)
                . '. Check-in: ' . $this->time($attendance->check_in_at)
                . '; check-out: ' . $this->time($attendance->check_out_at) . '.',
            $attendance
        );

        return back()->with('success', 'Attendance record added.');
    }

    // Correct the times of a record.
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $data = $request->validate($this->timeRules($request));

        // Remember the old times for the audit log.
        $oldIn  = $this->time($attendance->check_in_at);
        $oldOut = $this->time($attendance->check_out_at);

        $attendance->update([
            'check_in_at'  => $data['check_in_at'] ?? null,
            'check_out_at' => $data['check_out_at'] ?? null,
        ]);

        AuditLog::record(
            'attendance.updated',
            'Changed the times of ' . $this->describe($attendance)
                . '. Check-in: ' . $oldIn . ' to ' . $this->time($attendance->check_in_at)
                . '; check-out: ' . $oldOut . ' to ' . $this->time($attendance->check_out_at) . '.',
            $attendance
        );

        return back()->with('success', 'Attendance record updated.');
    }

    // Delete a record.
    public function destroy(Attendance $attendance): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        // Write the description first, because the row is about to disappear.
        $text = 'Deleted attendance of ' . $this->describe($attendance)
            . '. Check-in: ' . $this->time($attendance->check_in_at)
            . '; check-out: ' . $this->time($attendance->check_out_at) . '.';

        $attendance->delete();

        DashboardCache::adviser();

        AuditLog::record('attendance.deleted', $text, $attendance);

        return back()->with('success', 'Attendance record deleted.');
    }

    // "2026-0101 (Flow Tester), Flow Test Whole Day, Morning"
    private function describe(Attendance $attendance): string
    {
        $attendance->loadMissing('student.user', 'session.event');

        return $attendance->student->student_no
            . ' (' . $attendance->student->user->full_name . '), '
            . $attendance->session->event->title . ', '
            . Str::headline($attendance->session->period);
    }

    // A time as text, or "none" when it is empty.
    private function time($value): string
    {
        return $value ? $value->format('M d, h:i A') : 'none';
    }

    // Rules shared by store() and update().
    private function timeRules(Request $request): array
    {
        $outRules = ['nullable', 'date_format:Y-m-d\TH:i'];

        // A check-out cannot be earlier than the check-in (only checked when a check-in was typed).
        if ($request->filled('check_in_at')) {
            $outRules[] = 'after_or_equal:check_in_at';
        }

        return [
            // At least one of the two times must be filled.
            'check_in_at'  => ['nullable', 'required_without:check_out_at', 'date_format:Y-m-d\TH:i'],
            'check_out_at' => $outRules,
        ];
    }
}

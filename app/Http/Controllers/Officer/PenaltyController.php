<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Penalty;
use App\Models\Student;
use App\Notifications\SystemNotification;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PenaltyController extends Controller
{
    // Work out missed scans and fines for every student of one event.
    public function generate(Event $event): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        // Load the sessions together with their attendance rows.
        $event->load('sessions.attendances');

        // Every session needs a check-in and a check-out: two scans each.
        $required = $event->sessions->count() * 2;

        if ($required === 0) {
            return back()->with('error', 'This event has no sessions, so there is nothing to calculate.');
        }

        // Count the recorded scans per student: every non-null check_in_at and check_out_at.
        $counted = [];

        foreach ($event->sessions as $session) {
            foreach ($session->attendances as $attendance) {
                $counted[$attendance->student_id] = ($counted[$attendance->student_id] ?? 0)
                    + ($attendance->check_in_at ? 1 : 0)
                    + ($attendance->check_out_at ? 1 : 0);
            }
        }

        $finePerMiss = (float) $event->fine_per_miss;
        $withFine    = 0;
        $cleared     = 0;
        $notices     = []; // students whose fine is new or changed: they get a notification

        // One transaction: all penalties are updated, or none are.
        DB::transaction(function () use ($event, $required, $counted, $finePerMiss, &$withFine, &$cleared, &$notices) {
            foreach (Student::with('user')->get() as $student) {
                $missed = max(0, $required - ($counted[$student->id] ?? 0));

                // Full attendance: no penalty should exist.
                if ($missed === 0) {
                    Penalty::where('event_id', $event->id)
                        ->where('student_id', $student->id)
                        ->delete();
                    $cleared++;
                    continue;
                }

                // sanction and status are not listed here, so existing values are kept.
                $penalty = Penalty::updateOrCreate(
                    ['event_id' => $event->id, 'student_id' => $student->id],
                    [
                        'missed_scans' => $missed,
                        'amount'       => round($missed * $finePerMiss, 2),
                    ]
                );
                $withFine++;

                // Notify only when the fine is new or its numbers changed (no spam on a re-run).
                if ($penalty->wasRecentlyCreated || $penalty->wasChanged(['missed_scans', 'amount'])) {
                    $notices[] = [$student->user, $missed, (float) $penalty->amount];
                }
            }
        });

        // Send the notifications only after the transaction succeeded.
        foreach ($notices as [$user, $missed, $amount]) {
            $user->notify(new SystemNotification(
                'New fine posted',
                'You have a fine of ₱' . number_format($amount, 2) . " for {$event->title} ({$missed} missed scans).",
                route('student.my-fines', [], false)
            ));
        }

        DashboardCache::officer();
        DashboardCache::adviser();

        AuditLog::record(
            'fines.generated',
            "Generated fines for {$event->title}: {$withFine} student(s) with missed scans, {$cleared} with full attendance.",
            $event
        );

        return redirect()->route('penalties.index', $event)
            ->with('success', "Fines calculated: {$withFine} student(s) with missed scans, {$cleared} with full attendance.");
    }

    // Edit the amount, sanction and status of one penalty.
    public function update(Request $request, Penalty $penalty): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $data = $request->validate([
            'amount'   => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'sanction' => ['nullable', 'string', 'max:255'],
            'status'   => ['required', Rule::in(['unpaid', 'paid', 'served'])],
        ]);

        $penalty->loadMissing('student.user', 'event');

        // Remember the old values for the audit log.
        $oldAmount   = number_format((float) $penalty->amount, 2);
        $oldStatus   = $penalty->status;
        $oldSanction = blank($penalty->sanction) ? 'none' : $penalty->sanction;

        $penalty->update([
            'amount'   => $data['amount'],
            'sanction' => $data['sanction'] ?? null,
            'status'   => $data['status'],
        ]);

        DashboardCache::officer();
        DashboardCache::adviser();

        AuditLog::record(
            'penalty.updated',
            'Changed the fine of ' . $this->describe($penalty)
                . '. Amount: ' . $oldAmount . ' to ' . number_format((float) $penalty->amount, 2)
                . '; status: ' . $oldStatus . ' to ' . $penalty->status
                . '; sanction: ' . $oldSanction . ' to ' . (blank($penalty->sanction) ? 'none' : $penalty->sanction) . '.',
            $penalty
        );

        return back()->with('success', 'Penalty updated.');
    }

    // Delete one penalty.
    public function destroy(Penalty $penalty): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $penalty->loadMissing('student.user', 'event');

        // Write the description first, because the row is about to disappear.
        $text = 'Deleted the fine of ' . $this->describe($penalty)
            . ' (amount ' . number_format((float) $penalty->amount, 2) . ', status ' . $penalty->status . ').';

        $penalty->delete();

        DashboardCache::officer();
        DashboardCache::adviser();

        AuditLog::record('penalty.deleted', $text, $penalty);

        return back()->with('success', 'Penalty deleted.');
    }

    // "2026-0101 (Flow Tester) for Flow Test Whole Day"
    private function describe(Penalty $penalty): string
    {
        return $penalty->student->student_no
            . ' (' . $penalty->student->user->full_name . ') for '
            . $penalty->event->title;
    }
}

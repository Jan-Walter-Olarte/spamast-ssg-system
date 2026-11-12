<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Penalty;
use App\Models\Student;
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

        // One transaction: all penalties are updated, or none are.
        DB::transaction(function () use ($event, $required, $counted, $finePerMiss, &$withFine, &$cleared) {
            foreach (Student::pluck('id') as $studentId) {
                $missed = max(0, $required - ($counted[$studentId] ?? 0));

                // Full attendance: no penalty should exist.
                if ($missed === 0) {
                    Penalty::where('event_id', $event->id)
                        ->where('student_id', $studentId)
                        ->delete();
                    $cleared++;
                    continue;
                }

                // sanction and status are not listed here, so existing values are kept.
                Penalty::updateOrCreate(
                    ['event_id' => $event->id, 'student_id' => $studentId],
                    [
                        'missed_scans' => $missed,
                        'amount'       => round($missed * $finePerMiss, 2),
                    ]
                );
                $withFine++;
            }
        });

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

        $penalty->update([
            'amount'   => $data['amount'],
            'sanction' => $data['sanction'] ?? null,
            'status'   => $data['status'],
        ]);

        return back()->with('success', 'Penalty updated.');
    }

    // Delete one penalty.
    public function destroy(Penalty $penalty): RedirectResponse
    {
        Gate::authorize('manage-ssg');

        $penalty->delete();

        return back()->with('success', 'Penalty deleted.');
    }
}

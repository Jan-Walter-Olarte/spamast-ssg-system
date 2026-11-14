<?php

namespace Tests\Feature;

use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineCalculationTest extends TestCase
{
    use RefreshDatabase;

    // A whole-day event (4 scans required) with a fine of 50 per missed scan, and three students:
    //   $full   attended both sessions (4 scans)  -> no fine
    //   $half   attended only the morning (2)     -> 2 missed -> 100.00
    //   $absent never came (0)                    -> 4 missed -> 200.00
    private function setUpScenario(): array
    {
        $event = $this->makeEvent('whole_day', 'manual', 50);

        $full   = $this->makeStudent();
        $half   = $this->makeStudent();
        $absent = $this->makeStudent();

        foreach ($event->sessions as $session) {
            Attendance::create([
                'event_session_id' => $session->id,
                'student_id'       => $full->id,
                'check_in_at'      => now(),
                'check_out_at'     => now(),
            ]);
        }

        Attendance::create([
            'event_session_id' => $event->sessions->firstWhere('period', 'morning')->id,
            'student_id'       => $half->id,
            'check_in_at'      => now(),
            'check_out_at'     => now(),
        ]);

        return [$event, $full, $half, $absent];
    }

    public function test_fines_follow_missed_scans_times_the_fine_per_miss(): void
    {
        [$event, $full, $half, $absent] = $this->setUpScenario();

        $this->actingAs($this->makeUser('Officer'))
            ->post(route('officer.penalties.generate', $event))
            ->assertRedirect(route('penalties.index', $event));

        // Full attendance: no penalty row at all.
        $this->assertDatabaseMissing('penalties', ['event_id' => $event->id, 'student_id' => $full->id]);

        $this->assertDatabaseHas('penalties', [
            'event_id'     => $event->id,
            'student_id'   => $half->id,
            'missed_scans' => 2,
            'amount'       => 100,
            'status'       => 'unpaid',
        ]);

        $this->assertDatabaseHas('penalties', [
            'event_id'     => $event->id,
            'student_id'   => $absent->id,
            'missed_scans' => 4,
            'amount'       => 200,
        ]);

        $this->assertDatabaseCount('penalties', 2);
    }

    public function test_the_fines_page_total_matches_the_database(): void
    {
        [$event] = $this->setUpScenario();
        $officer = $this->makeUser('Officer');

        $this->actingAs($officer)->post(route('officer.penalties.generate', $event));

        // 100.00 + 200.00 = 300.00
        $this->actingAs($officer)
            ->get(route('penalties.index', $event))
            ->assertOk()
            ->assertSee('300.00');
    }

    public function test_running_it_twice_creates_no_duplicates_and_keeps_edits(): void
    {
        [$event, , $half] = $this->setUpScenario();
        $officer = $this->makeUser('Officer');

        $this->actingAs($officer)->post(route('officer.penalties.generate', $event));

        // The Officer adds a sanction and marks the fine as paid.
        $penalty = $event->penalties()->where('student_id', $half->id)->firstOrFail();

        $this->actingAs($officer)->put(route('officer.penalties.update', $penalty), [
            'amount'   => 100,
            'sanction' => 'Community service',
            'status'   => 'paid',
        ]);

        $this->actingAs($officer)->post(route('officer.penalties.generate', $event));

        $this->assertDatabaseCount('penalties', 2);
        $this->assertDatabaseHas('penalties', [
            'student_id' => $half->id,
            'sanction'   => 'Community service',
            'status'     => 'paid',
        ]);
    }

    public function test_a_student_who_later_gets_full_attendance_loses_the_fine(): void
    {
        [$event, , $half] = $this->setUpScenario();
        $officer = $this->makeUser('Officer');

        $this->actingAs($officer)->post(route('officer.penalties.generate', $event));
        $this->assertDatabaseHas('penalties', ['student_id' => $half->id]);

        // The afternoon session is added, so the student now has all 4 scans.
        Attendance::create([
            'event_session_id' => $event->sessions->firstWhere('period', 'afternoon')->id,
            'student_id'       => $half->id,
            'check_in_at'      => now(),
            'check_out_at'     => now(),
        ]);

        $this->actingAs($officer)->post(route('officer.penalties.generate', $event));

        $this->assertDatabaseMissing('penalties', ['event_id' => $event->id, 'student_id' => $half->id]);
    }

    public function test_generating_fines_writes_an_audit_entry_and_notifies_students(): void
    {
        [$event] = $this->setUpScenario();

        $this->actingAs($this->makeUser('Officer'))
            ->post(route('officer.penalties.generate', $event));

        $this->assertDatabaseHas('audit_logs', ['action' => 'fines.generated']);

        // Two students got a fine, so two notifications were created.
        $this->assertDatabaseCount('notifications', 2);
    }
}

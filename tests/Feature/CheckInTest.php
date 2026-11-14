<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    // A half-day event whose check-in and check-out are open, and a student.
    private function setUpOpenSession(): array
    {
        $event   = $this->makeEvent('half_day', 'manual');
        $session = $event->sessions->first();
        $session->update(['check_in_open' => true, 'check_out_open' => true]);

        return [$event, $session, $this->makeStudent()];
    }

    public function test_session_gets_a_check_in_code_automatically(): void
    {
        $event = $this->makeEvent('whole_day');

        foreach ($event->sessions as $session) {
            $this->assertNotNull($session->scan_code);
            $this->assertSame(6, strlen($session->scan_code));
        }

        $this->assertNotSame($event->sessions[0]->scan_code, $event->sessions[1]->scan_code);
    }

    public function test_student_checks_in_with_the_right_code(): void
    {
        [$event, $session, $student] = $this->setUpOpenSession();

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code])
            ->assertRedirect(route('events.show', $event))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'event_session_id' => $session->id,
            'student_id'       => $student->id,
        ]);
    }

    public function test_the_code_works_in_lowercase_and_with_spaces(): void
    {
        [, $session, $student] = $this->setUpOpenSession();

        $typed = ' ' . strtolower($session->scan_code) . ' ';

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $typed]);

        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_a_wrong_or_missing_code_creates_no_attendance(): void
    {
        [, $session, $student] = $this->setUpOpenSession();

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => 'WRONG1'])
            ->assertSessionHas('error');

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), [])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_checking_in_twice_creates_only_one_record(): void
    {
        [, $session, $student] = $this->setUpOpenSession();

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code]);

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_a_closed_session_refuses_check_in(): void
    {
        [, $session, $student] = $this->setUpOpenSession();
        $session->update(['check_in_open' => false]);

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code])
            ->assertForbidden();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_out_needs_a_check_in_first(): void
    {
        [, $session, $student] = $this->setUpOpenSession();

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-out', $session), ['code' => $session->scan_code])
            ->assertNotFound();
    }

    public function test_student_checks_in_then_out(): void
    {
        [, $session, $student] = $this->setUpOpenSession();

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code]);

        $this->actingAs($student->user)
            ->post(route('student.attendance.check-out', $session), ['code' => $session->scan_code])
            ->assertSessionHas('success');

        $attendance = Attendance::firstOrFail();

        $this->assertNotNull($attendance->check_in_at);
        $this->assertNotNull($attendance->check_out_at);
    }

    public function test_only_students_can_check_in(): void
    {
        [, $session] = $this->setUpOpenSession();

        $this->actingAs($this->makeUser('Officer'))
            ->post(route('student.attendance.check-in', $session), ['code' => $session->scan_code])
            ->assertForbidden();
    }
}

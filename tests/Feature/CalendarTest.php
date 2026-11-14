<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_adviser_and_student_can_open_the_calendar(): void
    {
        foreach (['Officer', 'Adviser', 'Student'] as $role) {
            $this->actingAs($this->makeUser($role))
                ->get(route('calendar.index'))
                ->assertOk()
                ->assertSee(today()->format('F Y'));
        }
    }

    public function test_the_admin_and_guests_cannot_open_the_calendar(): void
    {
        $this->get(route('calendar.index'))->assertRedirect(route('login'));

        $this->actingAs($this->makeUser('Admin'))
            ->get(route('calendar.index'))
            ->assertForbidden();
    }

    public function test_an_event_appears_on_the_calendar_of_its_month(): void
    {
        $this->makeEvent(); // an event for today, titled "Test Event"

        $this->actingAs($this->makeUser('Student'))
            ->get(route('calendar.index'))
            ->assertSee('Test Event');
    }

    public function test_an_event_from_two_months_ago_is_not_shown_this_month_but_is_shown_in_its_own_month(): void
    {
        $date = today()->subMonths(2)->startOfMonth()->addDays(10);

        Event::create([
            'title'         => 'Old Event Title',
            'venue'         => 'Old Hall',
            'event_date'    => $date->toDateString(),
            'day_type'      => 'half_day',
            'availability'  => 'manual',
            'fine_per_miss' => 0,
        ]);

        $student = $this->makeUser('Student');

        $this->actingAs($student)->get(route('calendar.index'))->assertDontSee('Old Event Title');

        $this->actingAs($student)
            ->get(route('calendar.index', ['month' => $date->format('Y-m')]))
            ->assertOk()
            ->assertSee('Old Event Title');
    }

    public function test_a_bad_month_value_falls_back_to_the_current_month(): void
    {
        $this->actingAs($this->makeUser('Officer'))
            ->get('/calendar?month=not-a-month')
            ->assertOk()
            ->assertSee(today()->format('F Y'));
    }
}

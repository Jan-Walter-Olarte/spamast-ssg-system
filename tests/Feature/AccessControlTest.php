<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $urls = [
            '/profile', '/events', '/announcements', '/reports', '/notifications',
            '/admin/dashboard', '/officer/dashboard', '/adviser/dashboard', '/student/dashboard',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_each_role_opens_its_own_dashboard(): void
    {
        $pages = [
            'Admin'   => '/admin/dashboard',
            'Officer' => '/officer/dashboard',
            'Adviser' => '/adviser/dashboard',
            'Student' => '/student/dashboard',
        ];

        foreach ($pages as $role => $url) {
            $this->actingAs($this->makeUser($role))->get($url)->assertOk();
        }
    }

    public function test_roles_get_403_on_pages_that_belong_to_other_roles(): void
    {
        $blocked = [
            'Student' => ['/admin/users', '/admin/audit-log', '/officer/students', '/reports'],
            'Adviser' => ['/admin/users', '/officer/students', '/officer/events/create', '/student/dashboard'],
            'Officer' => ['/admin/users', '/admin/institutes', '/adviser/dashboard', '/student/dashboard'],
            'Admin'   => ['/officer/students', '/events', '/reports', '/student/dashboard'],
        ];

        foreach ($blocked as $role => $urls) {
            $user = $this->makeUser($role);

            foreach ($urls as $url) {
                $this->actingAs($user)->get($url)->assertForbidden();
            }
        }
    }

    public function test_student_cannot_open_the_attendance_fines_or_report_of_an_event(): void
    {
        $event   = $this->makeEvent();
        $student = $this->makeStudent();

        $this->actingAs($student->user)->get(route('attendance.index', $event))->assertForbidden();
        $this->actingAs($student->user)->get(route('penalties.index', $event))->assertForbidden();
        $this->actingAs($student->user)->get(route('reports.show', $event))->assertForbidden();
    }

    public function test_adviser_can_view_but_not_manage(): void
    {
        $event   = $this->makeEvent();
        $adviser = $this->makeUser('Adviser');

        $this->actingAs($adviser)->get(route('attendance.index', $event))->assertOk();
        $this->actingAs($adviser)->get(route('penalties.index', $event))->assertOk();
        $this->actingAs($adviser)->get(route('reports.show', $event))->assertOk();

        $this->actingAs($adviser)
            ->post(route('officer.penalties.generate', $event))
            ->assertForbidden();
    }

    public function test_users_only_see_their_own_notifications_page(): void
    {
        $user = $this->makeUser('Student');

        $this->actingAs($user)->get(route('notifications.index'))->assertOk();
    }
}

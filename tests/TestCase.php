<?php

namespace Tests;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\Institute;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * SAFETY GUARD. This runs as soon as the application is built, BEFORE any table is erased.
     * Tests may only run on a database whose name ends in "_test".
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $connection = config('database.default');
        $database   = (string) config("database.connections.{$connection}.database");

        if (! str_ends_with($database, '_test')) {
            throw new \RuntimeException(
                "Refusing to run tests on the database [{$database}]. "
                . 'The tests erase tables, so the database name must end with "_test". '
                . 'Check phpunit.xml and run "php artisan optimize:clear".'
            );
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No test may call a real website (the weather widget, for example).
        Http::fake();
    }

    // Create a user with the given role. The password is "password123".
    protected function makeUser(string $role, array $attributes = []): User
    {
        $roleModel = Role::firstOrCreate(['name' => $role]);

        return User::create(array_merge([
            'first_name' => $role,
            'last_name'  => 'Tester',
            'email'      => strtolower($role) . '.' . uniqid() . '@test.local',
            'password'   => 'password123',
            'role_id'    => $roleModel->id,
            'is_active'  => true,
            'is_admin'   => false,
        ], $attributes));
    }

    // Create a Student account with its linked students row (the user is $student->user).
    protected function makeStudent(): Student
    {
        $institute = Institute::firstOrCreate(['code' => 'TST'], ['name' => 'Test Institute']);
        $program   = Program::firstOrCreate(
            ['code' => 'TSTP'],
            ['institute_id' => $institute->id, 'name' => 'Test Program']
        );

        $user = $this->makeUser('Student');

        return Student::create([
            'user_id'    => $user->id,
            'program_id' => $program->id,
            'student_no' => 'T-' . uniqid(),
            'sex'        => 'Male',
            'year_level' => 1,
        ]);
    }

    // Create an event for today with the sessions its day type needs.
    protected function makeEvent(string $dayType = 'whole_day', string $availability = 'manual', float $fine = 50): Event
    {
        $event = Event::create([
            'title'         => 'Test Event',
            'venue'         => 'Test Hall',
            'event_date'    => today()->toDateString(),
            'day_type'      => $dayType,
            'availability'  => $availability,
            'fine_per_miss' => $fine,
        ]);

        $periods = $dayType === 'whole_day' ? ['morning', 'afternoon'] : ['half_day'];

        foreach ($periods as $period) {
            // The check-in code is created automatically by the model.
            EventSession::create(['event_id' => $event->id, 'period' => $period]);
        }

        return $event->load('sessions');
    }
}

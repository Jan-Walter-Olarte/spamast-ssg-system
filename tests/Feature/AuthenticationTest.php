<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_opens_and_offers_password_recovery(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Forgot your password?');
    }

    public function test_correct_credentials_open_the_role_dashboard(): void
    {
        $user = $this->makeUser('Student');

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_each_role_is_sent_to_its_own_dashboard(): void
    {
        $expected = [
            'Admin'   => 'admin.dashboard',
            'Officer' => 'officer.dashboard',
            'Adviser' => 'adviser.dashboard',
            'Student' => 'student.dashboard',
        ];

        foreach ($expected as $role => $routeName) {
            $user = $this->makeUser($role);

            $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
                ->assertRedirect(route($routeName));

            $this->post('/logout'); // start the next role logged out
        }
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->makeUser('Student');

        $this->post('/login', ['email' => $user->email, 'password' => 'not-the-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        $user = $this->makeUser('Student', ['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_five_wrong_passwords_lock_the_login_even_for_the_right_password(): void
    {
        $user = $this->makeUser('Student');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_public_registration_creates_an_inactive_student(): void
    {
        Role::firstOrCreate(['name' => 'Student']);

        $this->post('/register', [
            'first_name'            => 'Pending',
            'last_name'             => 'Person',
            'email'                 => 'pending.person@test.local',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'pending.person@test.local')->firstOrFail();

        $this->assertFalse($user->is_active);
        $this->assertTrue($user->hasRole('Student'));
        $this->assertFalse($user->is_admin);
    }

    public function test_registration_cannot_pick_the_admin_role(): void
    {
        Role::firstOrCreate(['name' => 'Student']);
        Role::firstOrCreate(['name' => 'Admin']);

        $this->post('/register', [
            'first_name'            => 'Sneaky',
            'last_name'             => 'Person',
            'email'                 => 'sneaky@test.local',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Admin',
            'role_id'               => 1,
            'is_admin'              => 1,
        ]);

        $user = User::where('email', 'sneaky@test.local')->firstOrFail();

        $this->assertTrue($user->hasRole('Student'));
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->is_admin);
    }

    public function test_a_user_with_a_temporary_password_must_change_it_first(): void
    {
        $user = $this->makeUser('Student', ['must_change_password' => true]);

        $this->actingAs($user)
            ->get('/student/dashboard')
            ->assertRedirect(route('password.change'));

        $this->actingAs($user)
            ->get(route('password.change'))
            ->assertOk();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = $this->makeUser('Officer');

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

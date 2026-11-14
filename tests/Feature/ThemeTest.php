<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_save_a_theme_on_their_account(): void
    {
        $user = $this->makeUser('Student');

        $this->actingAs($user)
            ->patch(route('profile.theme'), ['theme' => 'saas'])
            ->assertNoContent();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'theme' => 'saas']);
    }

    public function test_an_unknown_theme_is_refused(): void
    {
        $user = $this->makeUser('Student');

        $this->actingAs($user)
            ->patch(route('profile.theme'), ['theme' => 'hacker-green'])
            ->assertSessionHasErrors('theme');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'theme' => null]);
    }

    public function test_a_guest_cannot_save_a_theme(): void
    {
        $this->patch(route('profile.theme'), ['theme' => 'red'])
            ->assertRedirect(route('login'));
    }

    public function test_a_user_can_only_change_their_own_theme(): void
    {
        $me    = $this->makeUser('Student');
        $other = $this->makeUser('Student');

        // Sending someone else's id has no effect: the theme is saved for the logged-in user only.
        $this->actingAs($me)
            ->patch(route('profile.theme'), ['theme' => 'red', 'id' => $other->id, 'user_id' => $other->id]);

        $this->assertDatabaseHas('users', ['id' => $me->id, 'theme' => 'red']);
        $this->assertDatabaseHas('users', ['id' => $other->id, 'theme' => null]);
    }

    public function test_the_saved_theme_is_sent_to_the_page(): void
    {
        $user = $this->makeUser('Student', ['theme' => 'saas']);

        $this->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('var account = "saas"', false);
    }

    public function test_a_user_with_no_saved_theme_gets_null(): void
    {
        $this->actingAs($this->makeUser('Student'))
            ->get(route('student.dashboard'))
            ->assertSee('var account = null', false);
    }

    public function test_a_user_with_a_temporary_password_can_still_save_a_theme(): void
    {
        $user = $this->makeUser('Student', ['must_change_password' => true]);

        $this->actingAs($user)
            ->patch(route('profile.theme'), ['theme' => 'minimal'])
            ->assertNoContent();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'theme' => 'minimal']);
    }
}

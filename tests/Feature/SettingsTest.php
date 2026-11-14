<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_are_used_before_anything_is_saved(): void
    {
        $this->assertSame('SPAMAST Supreme Student Government', Setting::schoolName());
        $this->assertSame('50.00', Setting::defaultFine());
    }

    public function test_only_the_admin_can_open_or_change_the_settings(): void
    {
        foreach (['Officer', 'Adviser', 'Student'] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)->get(route('admin.settings.edit'))->assertForbidden();

            $this->actingAs($user)
                ->put(route('admin.settings.update'), ['school_name' => 'Hacked', 'default_fine' => 1])
                ->assertForbidden();
        }

        $this->assertDatabaseCount('settings', 0);
    }

    // A separate test, so no earlier actingAs() is still logged in.
    public function test_a_guest_is_sent_to_the_login_page(): void
    {
        $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));

        $this->put(route('admin.settings.update'), ['school_name' => 'Hacked', 'default_fine' => 1])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_the_admin_can_save_the_settings(): void
    {
        $this->actingAs($this->makeUser('Admin'))
            ->put(route('admin.settings.update'), ['school_name' => 'New School Name', 'default_fine' => '75.5'])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('settings', ['name' => 'school_name', 'value' => 'New School Name']);
        $this->assertDatabaseHas('settings', ['name' => 'default_fine', 'value' => '75.50']);

        $this->assertSame('New School Name', Setting::schoolName());
        $this->assertSame('75.50', Setting::defaultFine());
    }

    public function test_bad_values_are_refused(): void
    {
        $admin = $this->makeUser('Admin');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), ['school_name' => '', 'default_fine' => -5])
            ->assertSessionHasErrors(['school_name', 'default_fine']);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), ['school_name' => 'Fine School', 'default_fine' => 'abc'])
            ->assertSessionHasErrors('default_fine');

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_saving_the_settings_is_written_to_the_audit_log(): void
    {
        $this->actingAs($this->makeUser('Admin'))
            ->put(route('admin.settings.update'), ['school_name' => 'Audited School', 'default_fine' => 20]);

        $this->assertDatabaseHas('audit_logs', ['action' => 'settings.updated']);
    }

    public function test_the_school_name_shows_in_the_landing_page_footer(): void
    {
        Setting::put('school_name', 'Footer Test School');

        $this->get('/')
            ->assertOk()
            ->assertSee('Footer Test School');
    }

    public function test_the_school_name_shows_in_the_report_heading(): void
    {
        Setting::put('school_name', 'Report Test School');

        $event = $this->makeEvent();

        $this->actingAs($this->makeUser('Officer'))
            ->get(route('reports.show', $event))
            ->assertOk()
            ->assertSee('Report Test School');
    }

    public function test_the_default_fine_prefills_a_new_event_but_not_an_existing_one(): void
    {
        Setting::put('default_fine', '75.00');

        $officer = $this->makeUser('Officer');
        $event   = $this->makeEvent('whole_day', 'manual', 20); // an existing event with its own fine

        $this->actingAs($officer)
            ->get(route('officer.events.create'))
            ->assertOk()
            ->assertSee('value="75.00"', false);

        $this->actingAs($officer)
            ->get(route('officer.events.edit', $event))
            ->assertOk()
            ->assertSee('value="20.00"', false);
    }
}

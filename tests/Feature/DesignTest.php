<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignTest extends TestCase
{
    use RefreshDatabase;

    private const THEMES = ['cinematic', 'saas', 'blue', 'friendly', 'minimal', 'red'];

    // Every color pair must stay readable: [text color, background color, smallest contrast allowed].
    private const READABLE = [
        ['text', 'bg', 7], ['text', 'surface', 7], ['text', 'surface-alt', 7],
        ['muted', 'surface', 4.5], ['muted', 'surface-alt', 4.5], ['muted', 'bg', 4.5],
        ['on-accent', 'accent', 4.5], ['link', 'surface', 4.5], ['link', 'bg', 4.5],
        ['nav-text', 'nav-bg', 7], ['side-text', 'side-bg', 4.5], ['hero-cta-text', 'hero-cta-bg', 4.5],
    ];

    // Read the twelve palettes (6 themes x light and dark) straight from the stylesheet.
    private function palettes(): array
    {
        $css = file_get_contents(public_path('css/app.css'));

        preg_match_all('/:root\[data-theme="(\w+)"\]\[data-mode="(\w+)"\]\s*\{([^}]*)\}/', $css, $blocks, PREG_SET_ORDER);

        $palettes = [];

        foreach ($blocks as [$whole, $theme, $mode, $body]) {
            preg_match_all('/--([a-z-]+):\s*(#[0-9A-Fa-f]{6})\s*;/', $body, $variables, PREG_SET_ORDER);

            foreach ($variables as [$line, $name, $hex]) {
                $palettes["{$theme}/{$mode}"][$name] = $hex;
            }
        }

        return $palettes;
    }

    // How bright a color is, from 0 (black) to 1 (white).
    private function luminance(string $hex): float
    {
        [$r, $g, $b] = array_map(function ($part) {
            $value = hexdec($part) / 255;

            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, str_split(ltrim($hex, '#'), 2));

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    // The contrast between two colors: 1 (identical) to 21 (black on white).
    private function contrast(string $first, string $second): float
    {
        $a = $this->luminance($first);
        $b = $this->luminance($second);

        return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
    }

    public function test_every_theme_has_a_light_and_a_dark_palette(): void
    {
        $palettes = $this->palettes();

        foreach (self::THEMES as $theme) {
            foreach (['light', 'dark'] as $mode) {
                $this->assertArrayHasKey("{$theme}/{$mode}", $palettes, "Missing palette: {$theme} {$mode}");
            }
        }

        $this->assertCount(12, $palettes);
    }

    public function test_every_palette_is_easy_to_read(): void
    {
        foreach ($this->palettes() as $name => $colors) {
            foreach (self::READABLE as [$front, $back, $minimum]) {
                $this->assertArrayHasKey($front, $colors, "{$name} has no --{$front}");
                $this->assertArrayHasKey($back, $colors, "{$name} has no --{$back}");

                $this->assertGreaterThanOrEqual(
                    $minimum,
                    $this->contrast($colors[$front], $colors[$back]),
                    "{$name}: --{$front} on --{$back} is too hard to read"
                );
            }
        }
    }

    public function test_the_minimalist_light_theme_is_softer_than_pure_white(): void
    {
        $light = $this->palettes()['minimal/light'];

        $this->assertLessThan(0.85, $this->luminance($light['bg']));
        $this->assertNotSame('#FFFFFF', strtoupper($light['surface']));
    }

    public function test_public_pages_offer_the_theme_menu_and_auto_dark_mode(): void
    {
        foreach (['/', '/login', '/register'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('data-theme-choice="minimal"', false)
                ->assertSee('data-mode-choice="auto"', false)
                ->assertSee('data-mode-choice="dark"', false)
                ->assertSee('prefers-color-scheme', false);
        }
    }

    public function test_each_role_sees_quick_actions_for_its_own_work_only(): void
    {
        $cases = [
            'Admin'   => [route('admin.settings.edit'),       route('officer.students.create')],
            'Officer' => [route('officer.students.import'),   route('admin.settings.edit')],
            'Adviser' => [route('reports.index'),             route('officer.students.create')],
            'Student' => [route('student.my-fines'),          route('reports.index')],
        ];

        foreach ($cases as $role => [$shown, $hidden]) {
            $this->actingAs($this->makeUser($role))
                ->get(route(strtolower($role) . '.dashboard'))
                ->assertOk()
                ->assertSee('Quick actions')
                ->assertSee($shown, false)
                ->assertDontSee($hidden, false);
        }
    }

    public function test_the_sidebar_is_grouped_and_has_icons(): void
    {
        $this->actingAs($this->makeUser('Officer'))
            ->get(route('officer.dashboard'))
            ->assertOk()
            ->assertSee('class="side-title"', false)
            ->assertSee('<svg class="icon"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_the_help_window_matches_the_role(): void
    {
        $this->actingAs($this->makeUser('Student'))
            ->get(route('student.dashboard'))
            ->assertSee('Check in to an event')
            ->assertDontSee('Approve a new account');

        $this->actingAs($this->makeUser('Admin'))
            ->get(route('admin.dashboard'))
            ->assertSee('Approve a new account')
            ->assertDontSee('Check in to an event');
    }

    public function test_no_dashboard_has_a_dead_link(): void
    {
        foreach (['Admin', 'Officer', 'Adviser', 'Student'] as $role) {
            $html = $this->actingAs($this->makeUser($role))
                ->get(route(strtolower($role) . '.dashboard'))
                ->assertOk()
                ->getContent();

            $this->assertStringNotContainsString('href="#"', $html, "{$role} dashboard has a dead link");
        }
    }

    public function test_breadcrumbs_show_where_you_are_but_not_on_the_dashboard(): void
    {
        $officer = $this->makeUser('Officer');

        $this->actingAs($officer)
            ->get(route('officer.events.create'))
            ->assertOk()
            ->assertSee('class="breadcrumbs"', false)
            ->assertSee('Add Event');

        $this->actingAs($officer)
            ->get(route('officer.dashboard'))
            ->assertDontSee('class="breadcrumbs"', false);
    }

    public function test_the_menu_and_help_buttons_exist_on_logged_in_pages(): void
    {
        $this->actingAs($this->makeUser('Adviser'))
            ->get(route('adviser.dashboard'))
            ->assertOk()
            ->assertSee('id="menu-btn"', false)
            ->assertSee('id="help-btn"', false)
            ->assertSee('<dialog', false);
    }

    public function test_error_pages_use_the_same_look(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('Page not found')
            ->assertSee('data-theme-choice="blue"', false);
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Officers manage the SSG work (events, students, fines, announcements).
        Gate::define('manage-ssg', function (User $user) {
            return $user->hasRole('Officer');
        });

        // Officers and Advisers may view reports.
        Gate::define('view-reports', function (User $user) {
            return $user->hasRole('Officer', 'Adviser');
        });

        // Students use attendance (check-in and check-out).
        Gate::define('use-attendance', function (User $user) {
            return $user->hasRole('Student');
        });

        // Whenever the weather partial is shown, give it the current weather.
        View::composer('partials.weather', function ($view) {
            $service = app(WeatherService::class);

            try {
                $weather = $service->current();
            } catch (\Throwable $e) {
                // Whatever goes wrong, the dashboard must still load.
                report($e);
                $weather = null;
            }

            $view->with([
                'weather'      => $weather,
                'weatherLabel' => $weather
                    ? $service->label(data_get($weather, 'weather_code') !== null ? (int) data_get($weather, 'weather_code') : null)
                    : null,
            ]);
        });
    }
}

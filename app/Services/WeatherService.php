<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    /**
     * The current weather as an array, or null when nothing is available.
     * Fresh data is cached for 30 minutes. If the website is down, the last
     * good answer (kept for one day) is returned instead.
     */
    public function current(): ?array
    {
        $weather = Cache::remember('weather.current', now()->addMinutes(30), function () {
            return $this->fetch();
        });

        // fetch() returned null: fall back to the last good answer, if there is one.
        return $weather ?? Cache::get('weather.last');
    }

    /**
     * Ask Open-Meteo for the current weather. Returns null on any failure.
     */
    private function fetch(): ?array
    {
        try {
            $response = Http::timeout(3)->get(config('services.weather.url'), [
                'latitude'  => config('services.weather.latitude'),
                'longitude' => config('services.weather.longitude'),
                'timezone'  => 'Asia/Manila',
                'current'   => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
            ]);
        } catch (ConnectionException) {
            // No internet, a timeout, or the website is unreachable.
            return null;
        }

        // The website answered, but with an error (for example 500).
        if ($response->failed()) {
            return null;
        }

        $current = $response->json('current');

        // A reply without the expected "current" part is treated as a failure.
        if (! is_array($current)) {
            return null;
        }

        // Keep this good answer for one day, as the backup.
        Cache::put('weather.last', $current, now()->addDay());

        return $current;
    }
}

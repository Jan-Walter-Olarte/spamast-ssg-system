<h2>Weather</h2>

<div class="panel panel-wide">
    @if (filled($weather))
        <div class="grid">
            <div class="card">
                <div class="num" style="font-size: 1.4rem;">{{ $weatherLabel }}</div>
                Condition
            </div>
            <div class="card">
                <div class="num">{{ number_format((float) data_get($weather, 'temperature_2m', 0), 1) }}&deg;C</div>
                Temperature
            </div>
            <div class="card">
                <div class="num">{{ number_format((float) data_get($weather, 'relative_humidity_2m', 0)) }}%</div>
                Humidity
            </div>
            <div class="card">
                <div class="num">{{ number_format((float) data_get($weather, 'wind_speed_10m', 0), 1) }} km/h</div>
                Wind speed
            </div>
        </div>
    @else
        <p>Weather is currently unavailable.</p>
    @endif

    <p class="hint" style="margin-top: 12px;">
        Weather data by <a href="https://open-meteo.com/" target="_blank" rel="noopener">Open-Meteo</a>
    </p>
</div>

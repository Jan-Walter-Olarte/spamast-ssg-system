@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $first->format('F Y') }}</h1>
            <p style="margin: 0; color: var(--muted);">{{ $count }} {{ Str::plural('event', $count) }} this month</p>
        </div>

        <div class="actions">
            <a href="{{ route('calendar.index', ['month' => $previous]) }}" class="btn btn-light">&larr; Previous</a>
            <a href="{{ route('calendar.index') }}" class="btn btn-light">Today</a>
            <a href="{{ route('calendar.index', ['month' => $next]) }}" class="btn btn-light">Next &rarr;</a>
            <a href="{{ route('events.index') }}" class="btn">List view</a>
        </div>
    </div>

    <div class="table-wrap">
        <div class="calendar">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                <div class="cal-head">{{ $dayName }}</div>
            @endforeach

            @foreach ($weeks as $week)
                @foreach ($week as $day)
                    <div class="cal-cell {{ $day['in_month'] ? '' : 'cal-out' }} {{ $day['is_today'] ? 'cal-today' : '' }}">
                        <div class="cal-day">{{ $day['date']->day }}</div>

                        @foreach ($day['events'] as $event)
                            <a href="{{ route('events.show', $event->id) }}"
                               class="cal-event {{ $event->day_type === 'half_day' ? 'cal-event-half' : '' }}"
                               title="{{ $event->title }} - {{ $event->venue }} ({{ Str::headline($event->day_type) }})">
                                {{ $event->title }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="cal-legend">
        <span><span class="cal-swatch" style="background: var(--accent);"></span>Whole day</span>
        <span><span class="cal-swatch" style="background: var(--amber);"></span>Half day</span>
        <span>Outlined cell = today</span>
    </div>
@endsection

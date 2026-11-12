@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="page-head">
        <h1>{{ $event->title }}</h1>
        <div class="actions">
            <a href="{{ route('events.index') }}" class="btn btn-light">Back to events</a>
            @can('update', $event)
                <a href="{{ route('officer.events.edit', $event) }}" class="btn">Edit</a>
            @endcan
        </div>
    </div>

    <div class="panel panel-wide">
        <div class="grid">
            <div class="card"><h3>Date</h3>{{ $event->event_date->format('F d, Y') }}</div>
            <div class="card"><h3>Venue</h3>{{ $event->venue }}</div>
            <div class="card"><h3>Day type</h3>{{ ucwords(str_replace('_', ' ', $event->day_type)) }}</div>
            <div class="card"><h3>Availability</h3>{{ ucfirst($event->availability) }}</div>
            <div class="card"><h3>Fine per missed scan</h3>{{ $event->fine_per_miss }}</div>
            <div class="card"><h3>Created by</h3>{{ $event->creator?->first_name }} {{ $event->creator?->last_name }}</div>
        </div>

        @if ($event->description)
            <h2>Description</h2>
            <p>{{ $event->description }}</p>
        @endif

        <h2>Sessions</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>Check-in window</th>
                        <th>Check-out window</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event->sessions as $session)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $session->period)) }}</td>
                            <td>
                                @if ($session->check_in_start)
                                    {{ substr($session->check_in_start, 0, 5) }} to {{ substr($session->check_in_end, 0, 5) }}
                                @else
                                    Not set
                                @endif
                            </td>
                            <td>
                                @if ($session->check_out_start)
                                    {{ substr($session->check_out_start, 0, 5) }} to {{ substr($session->check_out_end, 0, 5) }}
                                @else
                                    Not set
                                @endif
                            </td>
                            <td><span class="badge {{ $session->check_in_open ? 'badge-success' : 'badge-warning' }}">{{ $session->check_in_open ? 'Open' : 'Closed' }}</span></td>
                            <td><span class="badge {{ $session->check_out_open ? 'badge-success' : 'badge-warning' }}">{{ $session->check_out_open ? 'Open' : 'Closed' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

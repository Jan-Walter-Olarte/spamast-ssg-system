@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="page-head">
        <h1>{{ $event->title }}</h1>
        <div class="actions">
            <a href="{{ route('events.index') }}" class="btn btn-light">Back to events</a>
            @can('view-reports')
                <a href="{{ route('attendance.index', $event) }}" class="btn btn-light">Attendance</a>
            @endcan
            @can('update', $event)
                <a href="{{ route('officer.events.edit', $event) }}" class="btn">Edit</a>
            @endcan
        </div>
    </div>

    {{-- A Student account that has no linked student record --}}
    @if ($isStudent && ! $student)
        <div class="alert alert-warning">
            Your account is not linked to a student record yet, so you cannot check in. Please ask an Officer to add you as a student.
        </div>
    @endif

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

                        {{-- Officers of a manual event --}}
                        @if ($event->availability === 'manual')
                            @can('update', $event)
                                <th>Actions</th>
                            @endcan
                        @endif

                        {{-- Students with a linked record --}}
                        @if ($student)
                            <th>My check-in</th>
                            <th>My check-out</th>
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event->sessions as $session)
                        @php($mine = $myAttendances[$session->id] ?? null)
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
                            <td>
                                <span class="badge {{ $session->isCheckInOpen() ? 'badge-success' : 'badge-warning' }}">
                                    {{ $session->isCheckInOpen() ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $session->isCheckOutOpen() ? 'badge-success' : 'badge-warning' }}">
                                    {{ $session->isCheckOutOpen() ? 'Open' : 'Closed' }}
                                </span>
                            </td>

                            {{-- Open and Close buttons: Officers, manual events only --}}
                            @if ($event->availability === 'manual')
                                @can('update', $event)
                                    <td>
                                        <div class="actions">
                                            <form method="POST" action="{{ route('officer.sessions.toggle', $session) }}" class="inline-form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="check_in">
                                                <button type="submit" class="btn btn-light btn-sm">
                                                    {{ $session->check_in_open ? 'Close check-in' : 'Open check-in' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('officer.sessions.toggle', $session) }}" class="inline-form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="check_out">
                                                <button type="submit" class="btn btn-light btn-sm">
                                                    {{ $session->check_out_open ? 'Close check-out' : 'Open check-out' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endcan
                            @endif

                            {{-- Students: their own times and only the buttons that apply right now --}}
                            @if ($student)
                                <td>{{ $mine?->check_in_at ? $mine->check_in_at->format('h:i A') : '-' }}</td>
                                <td>{{ $mine?->check_out_at ? $mine->check_out_at->format('h:i A') : '-' }}</td>
                                <td>
                                    <div class="actions">
                                        @if ($session->isCheckInOpen() && ! $mine)
                                            <form method="POST" action="{{ route('student.attendance.check-in', $session) }}" class="inline-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm">Check in</button>
                                            </form>
                                        @endif

                                        @if ($session->isCheckOutOpen() && $mine && ! $mine->check_out_at)
                                            <form method="POST" action="{{ route('student.attendance.check-out', $session) }}" class="inline-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm">Check out</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

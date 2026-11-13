@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="page-head">
        <h1>{{ $event->title }}</h1>
        <div class="actions">
            <a href="{{ route('events.index') }}" class="btn btn-light">Back to events</a>

            {{-- Officers and Advisers: view the records --}}
            @can('view-reports')
                <a href="{{ route('attendance.index', $event) }}" class="btn btn-light">Attendance</a>
                <a href="{{ route('penalties.index', $event) }}" class="btn btn-light">Fines</a>
                <a href="{{ route('reports.show', $event) }}" class="btn btn-light">Report</a>
            @endcan

            {{-- Officers only --}}
            @can('manage-ssg')
                <a href="{{ route('officer.events.edit', $event) }}" class="btn">Edit</a>
            @endcan
        </div>
    </div>

    {{-- Students whose account has no linked student record --}}
    @can('use-attendance')
        @if (! $student)
            <div class="alert alert-warning">
                Your account is not linked to a student record yet, so you cannot check in. Please ask an Officer to add you as a student.
            </div>
        @else
            <div class="alert">
                To check in or check out, type the <strong>code shown at the venue</strong> next to the button.
            </div>
        @endif
    @endcan

    <div class="panel panel-wide">
        <div class="grid">
            <div class="card"><h3>Date</h3>{{ $event->event_date->format('F d, Y') }}</div>
            <div class="card"><h3>Venue</h3>{{ $event->venue }}</div>
            <div class="card"><h3>Day type</h3>{{ Str::headline($event->day_type) }}</div>
            <div class="card"><h3>Availability</h3>{{ Str::headline($event->availability) }}</div>
            <div class="card"><h3>Fine per missed scan</h3>&#8369;{{ number_format((float) $event->fine_per_miss, 2) }}</div>
            <div class="card"><h3>Created by</h3>{{ $event->creator?->full_name }}</div>
        </div>

        @if ($event->description)
            <h2>Description</h2>
            <p>{{ $event->description }}</p>
        @endif

        <h2>Session schedule</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>Check-in window</th>
                        <th>Check-out window</th>
                        <th>Check-in</th>
                        <th>Check-out</th>

                        {{-- Officers: the code to show at the venue --}}
                        @can('manage-ssg')
                            <th>Check-in code</th>
                        @endcan

                        {{-- Officers of a manual event --}}
                        @can('manage-ssg')
                            @if ($event->availability === 'manual')
                                <th>Actions</th>
                            @endif
                        @endcan

                        {{-- Students with a linked record --}}
                        @can('use-attendance')
                            @if ($student)
                                <th>My check-in</th>
                                <th>My check-out</th>
                                <th>Actions</th>
                            @endif
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event->sessions as $session)
                        @php($mine = $myAttendances[$session->id] ?? null)
                        <tr>
                            <td>{{ Str::headline($session->period) }}</td>
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

                            {{-- Officer: the code, and a button to make a new one --}}
                            @can('manage-ssg')
                                <td>
                                    <strong style="letter-spacing: 0.15em; font-size: 1.1rem;">{{ $session->scan_code }}</strong>
                                    <form method="POST" action="{{ route('officer.sessions.code', $session) }}" class="inline-form"
                                          onsubmit="return confirm('Generate a new code? The old code will stop working.');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-light btn-sm">New code</button>
                                    </form>
                                </td>
                            @endcan

                            {{-- Officer controls: open or close a manual session --}}
                            @can('manage-ssg')
                                @if ($event->availability === 'manual')
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
                                @endif
                            @endcan

                            {{-- Student controls: their own times, and the code box with the buttons that apply right now --}}
                            @can('use-attendance')
                                @if ($student)
                                    <td>{{ $mine?->check_in_at ? $mine->check_in_at->format('h:i A') : '-' }}</td>
                                    <td>{{ $mine?->check_out_at ? $mine->check_out_at->format('h:i A') : '-' }}</td>
                                    <td>
                                        <div class="actions">
                                            @if ($session->isCheckInOpen() && ! $mine)
                                                <form method="POST" action="{{ route('student.attendance.check-in', $session) }}" class="inline-form">
                                                    @csrf
                                                    <input type="text" name="code" maxlength="6" placeholder="Code" required autocomplete="off"
                                                           style="width: 90px; text-transform: uppercase;">
                                                    <button type="submit" class="btn btn-sm">Check in</button>
                                                </form>
                                            @endif

                                            @if ($session->isCheckOutOpen() && $mine && ! $mine->check_out_at)
                                                <form method="POST" action="{{ route('student.attendance.check-out', $session) }}" class="inline-form">
                                                    @csrf
                                                    <input type="text" name="code" maxlength="6" placeholder="Code" required autocomplete="off"
                                                           style="width: 90px; text-transform: uppercase;">
                                                    <button type="submit" class="btn btn-sm">Check out</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

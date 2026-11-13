@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
    <div class="page-head">
        <h1>My Attendance</h1>
    </div>

    @if (! $student)
        <div class="alert alert-warning">
            Your account is not linked to a student record yet. Please ask an Officer to add you as a student.
        </div>
    @else
        <div class="grid" style="margin-bottom: 20px;">
            <div class="card"><div class="num">{{ $attendances->total() }}</div>Total records</div>
            <div class="card"><div class="num">{{ $complete }}</div>Complete (in and out)</div>
            <div class="card"><div class="num">{{ $incomplete }}</div>Missing a check-in or check-out</div>
        </div>

        <div class="panel panel-wide">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td>
                                    <a href="{{ route('events.show', $attendance->session->event_id) }}">{{ $attendance->session->event->title }}</a>
                                </td>
                                <td>{{ $attendance->session->event->event_date->format('M d, Y') }}</td>
                                <td>{{ Str::headline($attendance->session->period) }}</td>
                                <td>{{ $attendance->check_in_at ? $attendance->check_in_at->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->check_out_at ? $attendance->check_out_at->format('h:i A') : '-' }}</td>
                                <td>
                                    @if ($attendance->check_in_at && $attendance->check_out_at)
                                        <span class="badge badge-success">Complete</span>
                                    @elseif ($attendance->check_in_at)
                                        <span class="badge badge-warning">No check-out</span>
                                    @else
                                        <span class="badge badge-warning">No check-in</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">You have no attendance records yet. Check in at an event to see it here.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pager">
                @if ($attendances->onFirstPage())
                    <span class="btn btn-light btn-sm btn-disabled">Previous</span>
                @else
                    <a href="{{ $attendances->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
                @endif

                <span>Page {{ $attendances->currentPage() }} of {{ $attendances->lastPage() }}</span>

                @if ($attendances->hasMorePages())
                    <a href="{{ $attendances->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
                @else
                    <span class="btn btn-light btn-sm btn-disabled">Next</span>
                @endif
            </div>
        </div>
    @endif
@endsection

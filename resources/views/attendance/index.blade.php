@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <div class="page-head">
        <div>
            <h1>Attendance</h1>
            <p style="margin: 0; color: var(--muted);">{{ $event->title }} &middot; {{ $event->event_date->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('events.show', $event) }}" class="btn btn-light">Back to event</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- A note when the filters were restored from the session --}}
    @if ($restored)
        <div class="alert">Showing your saved filters from your last visit. Use "Clear filters" to remove them.</div>
    @endif

    {{-- Filters (a GET form, so the address keeps them) --}}
    <form method="GET" action="{{ route('attendance.index', $event) }}" class="toolbar">
        <select name="session">
            <option value="">All sessions</option>
            @foreach ($event->sessions as $session)
                <option value="{{ $session->id }}" @selected((string) $sessionId === (string) $session->id)>
                    {{ Str::headline($session->period) }}
                </option>
            @endforeach
        </select>

        <select name="institute" id="filter_institute" data-institute-select data-target="#filter_program">
            <option value="">All institutes</option>
            @foreach ($institutes as $institute)
                <option value="{{ $institute->id }}" @selected((string) $instituteId === (string) $institute->id)>{{ $institute->code }}</option>
            @endforeach
        </select>

        <select name="program" id="filter_program">
            <option value="">All programs</option>
            @foreach ($programs as $program)
                <option value="{{ $program->id }}" data-institute="{{ $program->institute_id }}"
                    @selected((string) $programId === (string) $program->id)>{{ $program->code }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('attendance.index', [$event, 'clear' => 1]) }}" class="btn btn-light">Clear filters</a>
    </form>

    {{-- Officers only: add a missing record --}}
    @can('manage-ssg')
        <div class="panel panel-wide" style="margin-bottom: 16px;">
            <details>
                <summary>Add a missing record</summary>
                <form method="POST" action="{{ route('officer.attendance.store', $event) }}" class="inline-edit">
                    @csrf

                    <label for="event_session_id">Session</label>
                    <select id="event_session_id" name="event_session_id" required>
                        <option value="">Select a session</option>
                        @foreach ($event->sessions as $session)
                            <option value="{{ $session->id }}" @selected((string) old('event_session_id') === (string) $session->id)>
                                {{ Str::headline($session->period) }}
                            </option>
                        @endforeach
                    </select>

                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                {{ $student->student_no }} - {{ $student->user->last_name }}, {{ $student->user->first_name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="new_in">Check-in time</label>
                    <input type="datetime-local" id="new_in" name="check_in_at" value="{{ old('check_in_at') }}">

                    <label for="new_out">Check-out time</label>
                    <input type="datetime-local" id="new_out" name="check_out_at" value="{{ old('check_out_at') }}">

                    <button type="submit" class="btn btn-sm">Add record</button>
                </form>
            </details>
        </div>
    @endcan

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student no.</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Institute</th>
                        <th>Session</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        @can('manage-ssg')
                            <th>Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->student->student_no }}</td>
                            <td>{{ $attendance->student->user->last_name }}, {{ $attendance->student->user->first_name }}</td>
                            <td>{{ $attendance->student->program->code }}</td>
                            <td><span class="badge badge-info">{{ $attendance->student->program->institute->code }}</span></td>
                            <td>{{ Str::headline($attendance->session->period) }}</td>
                            <td>{{ $attendance->check_in_at ? $attendance->check_in_at->format('M d, h:i A') : '-' }}</td>
                            <td>{{ $attendance->check_out_at ? $attendance->check_out_at->format('M d, h:i A') : '-' }}</td>

                            {{-- Officer controls: Advisers never see these --}}
                            @can('manage-ssg')
                                <td>
                                    <details>
                                        <summary>Edit</summary>
                                        <form method="POST" action="{{ route('officer.attendance.update', $attendance) }}" class="inline-edit">
                                            @csrf
                                            @method('PUT')

                                            <label>Check-in time</label>
                                            <input type="datetime-local" name="check_in_at"
                                                   value="{{ $attendance->check_in_at?->format('Y-m-d\TH:i') }}">

                                            <label>Check-out time</label>
                                            <input type="datetime-local" name="check_out_at"
                                                   value="{{ $attendance->check_out_at?->format('Y-m-d\TH:i') }}">

                                            <button type="submit" class="btn btn-sm">Save</button>
                                        </form>
                                    </details>

                                    <form method="POST" action="{{ route('officer.attendance.destroy', $attendance) }}" class="inline-form"
                                          onsubmit="return confirm('Delete this attendance record? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="margin-top: 6px;">Delete</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="8">No attendance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($attendances->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $attendances->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $attendances->currentPage() }} of {{ $attendances->lastPage() }} ({{ $attendances->total() }} records)</span>

            @if ($attendances->hasMorePages())
                <a href="{{ $attendances->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>

    {{-- Reuses the institute-to-program filter script from the Students page --}}
    @include('officer.students.program-filter')
@endsection

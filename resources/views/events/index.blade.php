@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <div class="page-head">
        <h1>Events</h1>
        @can('manage-ssg')
            <a href="{{ route('officer.events.create') }}" class="btn">Add Event</a>
        @endcan
    </div>

    {{-- Upcoming / Past tabs --}}
    <div class="tabs">
        <a href="{{ route('events.index', ['tab' => 'upcoming']) }}" class="{{ ! $date && $tab === 'upcoming' ? 'active' : '' }}">Upcoming</a>
        <a href="{{ route('events.index', ['tab' => 'past']) }}" class="{{ ! $date && $tab === 'past' ? 'active' : '' }}">Past</a>
    </div>

    {{-- Text and date search (a GET form, so the address keeps them) --}}
    <form method="GET" action="{{ route('events.index') }}" class="toolbar">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search title or venue">
        <input type="date" name="date" value="{{ $date }}" title="Show events on this date">
        <button type="submit" class="btn">Search</button>
        <a href="{{ route('events.index', ['tab' => $tab]) }}" class="btn btn-light">Reset</a>
    </form>

    @if ($date)
        <div class="alert">
            Showing events on {{ \Illuminate\Support\Carbon::parse($date)->format('F d, Y') }}. Clear the date to use the tabs again.
        </div>
    @endif

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Venue</th>
                        <th>Type</th>
                        <th>Availability</th>
                        <th>Fine</th>
                        {{-- Attendance counts are for Officers and Advisers only --}}
                        @can('view-reports')
                            <th>Scans</th>
                        @endcan
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->event_date->format('M d, Y') }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->venue }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $event->day_type)) }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($event->availability) }}</span></td>
                            <td>&#8369;{{ number_format((float) $event->fine_per_miss, 2) }}</td>
                            @can('view-reports')
                                <td>{{ $event->attendances_count }}</td>
                            @endcan
                            <td>
                                <div class="actions">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-light btn-sm">View</a>

                                    {{-- Officer controls: Advisers and Students never see these --}}
                                    @can('manage-ssg')
                                        <a href="{{ route('officer.events.edit', $event) }}" class="btn btn-light btn-sm">Edit</a>

                                        @can('delete', $event)
                                            <form method="POST" action="{{ route('officer.events.destroy', $event) }}" class="inline-form"
                                                  onsubmit="return confirm('Delete this event and its sessions? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        @endcan
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('view-reports') ? 8 : 7 }}">
                                No {{ $date ? '' : $tab }} events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            @if ($events->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $events->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $events->currentPage() }} of {{ $events->lastPage() }} ({{ $events->total() }} events)</span>

            @if ($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection

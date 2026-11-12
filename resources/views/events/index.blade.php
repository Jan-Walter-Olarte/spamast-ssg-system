@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <div class="page-head">
        <h1>Events</h1>
        @can('manage-ssg')
            <a href="{{ route('officer.events.create') }}" class="btn">Add Event</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('events.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search title or venue">
        <button type="submit" class="btn">Search</button>
        <a href="{{ route('events.index') }}" class="btn btn-light">Reset</a>
    </form>

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
                        <th>Scans</th>
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
                            <td>{{ $event->fine_per_miss }}</td>
                            <td>{{ $event->attendances_count }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-light btn-sm">View</a>

                                    @can('update', $event)
                                        <a href="{{ route('officer.events.edit', $event) }}" class="btn btn-light btn-sm">Edit</a>
                                    @endcan

                                    @can('delete', $event)
                                        <form method="POST" action="{{ route('officer.events.destroy', $event) }}" class="inline-form"
                                              onsubmit="return confirm('Delete this event and its sessions? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No events found.</td></tr>
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

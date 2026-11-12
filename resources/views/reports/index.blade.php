@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="page-head">
        <h1>Reports</h1>
    </div>

    <form method="GET" action="{{ route('reports.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search title or venue">
        <button type="submit" class="btn">Search</button>
        <a href="{{ route('reports.index') }}" class="btn btn-light">Reset</a>
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
                            <td>{{ $event->attendances_count }}</td>
                            <td>
                                <a href="{{ route('reports.show', $event) }}" class="btn btn-light btn-sm">View report</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No events found.</td></tr>
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

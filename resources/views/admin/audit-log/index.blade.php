@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="page-head">
        <h1>Audit Log</h1>
    </div>

    <p style="color: var(--muted);">A read-only record of who changed what. Entries cannot be edited or deleted.</p>

    <form method="GET" action="{{ route('admin.audit-log.index') }}" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search the description">
        <select name="action">
            <option value="">All actions</option>
            @foreach ($actions as $item)
                <option value="{{ $item }}" @selected($action === $item)>{{ Str::headline(str_replace('.', ' ', $item)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('admin.audit-log.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Who</th>
                        <th>Action</th>
                        <th>What happened</th>
                        <th>IP address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            $badge = str_ends_with($log->action, '.deleted') ? 'badge-danger'
                                : (str_ends_with($log->action, '.activated') ? 'badge-success' : 'badge-info');
                        @endphp
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $log->user?->full_name ?? 'Deleted user' }}</td>
                            <td><span class="badge {{ $badge }}">{{ Str::headline(str_replace('.', ' ', $log->action)) }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            @if ($logs->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }} ({{ $logs->total() }} entries)</span>

            @if ($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <div class="page-head">
        <h1>Announcements</h1>
        @can('manage-ssg')
            <a href="{{ route('officer.announcements.create') }}" class="btn">Post Announcement</a>
        @endcan
    </div>

    <div class="panel panel-wide">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Posted by</th>
                        <th>Date</th>
                        <th>Attachment</th>
                        @can('manage-ssg')
                            <th>Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $announcement)
                        <tr>
                            <td>
                                <a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a>
                                <br>
                                <span style="color: var(--muted); font-size: 0.85rem;">{{ Str::limit($announcement->body, 120) }}</span>
                            </td>
                            <td>{{ $announcement->user->full_name }}</td>
                            <td>{{ $announcement->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if ($announcement->attachment)
                                    <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank">Open file</a>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Officer controls: Advisers and Students never see these --}}
                            @can('manage-ssg')
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('officer.announcements.edit', $announcement) }}" class="btn btn-light btn-sm">Edit</a>

                                        <form method="POST" action="{{ route('officer.announcements.destroy', $announcement) }}" class="inline-form"
                                              onsubmit="return confirm('Delete this announcement and its file? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="5">No announcements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Simple pagination --}}
        <div class="pager">
            @if ($announcements->onFirstPage())
                <span class="btn btn-light btn-sm btn-disabled">Previous</span>
            @else
                <a href="{{ $announcements->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
            @endif

            <span>Page {{ $announcements->currentPage() }} of {{ $announcements->lastPage() }} ({{ $announcements->total() }} announcements)</span>

            @if ($announcements->hasMorePages())
                <a href="{{ $announcements->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
            @else
                <span class="btn btn-light btn-sm btn-disabled">Next</span>
            @endif
        </div>
    </div>
@endsection

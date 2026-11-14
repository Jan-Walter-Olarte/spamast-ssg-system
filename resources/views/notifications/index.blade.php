@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="page-head">
        <h1>Notifications</h1>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}" class="inline-form">
                @csrf
                <button type="submit" class="btn btn-light">Mark all as read ({{ $unreadCount }})</button>
            </form>
        @endif
    </div>

    <div class="panel panel-wide" style="padding: 0;">
        @forelse ($notifications as $notification)
            <div class="notice {{ $notification->read_at ? '' : 'notice-unread' }}">
                <div>
                    <div class="notice-title">{{ data_get($notification->data, 'title') }}</div>
                    <div>{{ data_get($notification->data, 'message') }}</div>
                    <div class="notice-meta">{{ $notification->created_at->diffForHumans() }}</div>
                </div>

                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $notification->read_at ? 'btn-light' : '' }}">
                        {{ data_get($notification->data, 'url') ? 'Open' : 'Mark as read' }}
                    </button>
                </form>
            </div>
        @empty
            <p style="padding: 20px;">You have no notifications yet.</p>
        @endforelse
    </div>

    <div class="pager">
        @if ($notifications->onFirstPage())
            <span class="btn btn-light btn-sm btn-disabled">Previous</span>
        @else
            <a href="{{ $notifications->previousPageUrl() }}" class="btn btn-light btn-sm">Previous</a>
        @endif

        <span>Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}</span>

        @if ($notifications->hasMorePages())
            <a href="{{ $notifications->nextPageUrl() }}" class="btn btn-light btn-sm">Next</a>
        @else
            <span class="btn btn-light btn-sm btn-disabled">Next</span>
        @endif
    </div>
@endsection

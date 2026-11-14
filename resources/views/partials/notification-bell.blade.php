@php($unread = auth()->user()->unreadNotifications()->count())

<a href="{{ route('notifications.index') }}" class="bell" title="Notifications" aria-label="Notifications">
    <span aria-hidden="true">&#128276;</span>
    @if ($unread > 0)
        <span class="bell-count">{{ $unread > 99 ? '99+' : $unread }}</span>
    @endif
</a>

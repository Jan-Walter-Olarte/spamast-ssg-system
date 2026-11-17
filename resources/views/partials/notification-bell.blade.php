@php($unread = auth()->user()->unreadNotifications()->count())

<a href="{{ route('notifications.index') }}" class="tool-btn bell" title="Notifications"
   aria-label="Notifications{{ $unread > 0 ? ' (' . $unread . ' unread)' : '' }}">
    @include('partials.icon', ['name' => 'bell'])
    @if ($unread > 0)
        <span class="bell-count">{{ $unread > 99 ? '99+' : $unread }}</span>
    @endif
</a>

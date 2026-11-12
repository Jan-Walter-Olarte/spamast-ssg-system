<h2>Latest announcements</h2>

<div class="panel panel-wide">
    @forelse ($latestAnnouncements as $announcement)
        <p style="margin-bottom: 12px;">
            <a href="{{ route('announcements.show', $announcement) }}"><strong>{{ $announcement->title }}</strong></a><br>
            <span style="color: var(--muted); font-size: 0.85rem;">
                {{ $announcement->user->full_name }}
                &middot; {{ $announcement->created_at->format('M d, Y') }}
            </span><br>
            {{ Str::limit($announcement->body, 120) }}
        </p>
    @empty
        <p>No announcements yet.</p>
    @endforelse

    <a href="{{ route('announcements.index') }}" class="btn btn-light btn-sm">View all announcements</a>
</div>

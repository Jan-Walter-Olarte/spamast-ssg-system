<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if (isset($announcement))
        @method('PUT')
    @endif

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" required>

    <label for="body">Announcement</label>
    <textarea id="body" name="body" rows="8" required>{{ old('body', $announcement->body ?? '') }}</textarea>

    <label for="attachment">Attachment (optional)</label>
    <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp">
    <p class="hint">PDF, JPG, PNG or WEBP, up to 5 MB.</p>

    @if (isset($announcement) && $announcement->attachment)
        <p class="hint">
            Current file:
            <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank">{{ basename($announcement->attachment) }}</a>.
            Choosing a new file replaces it.
        </p>
    @endif

    <button type="submit" class="btn" style="margin-top: 20px;">{{ isset($announcement) ? 'Save changes' : 'Post announcement' }}</button>
    <a href="{{ route('announcements.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

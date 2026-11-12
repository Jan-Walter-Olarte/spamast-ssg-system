<form method="POST" action="{{ $action }}">
    @csrf
    @if (isset($institute))
        @method('PUT')
    @endif

    <label for="code">Code</label>
    <input type="text" id="code" name="code" value="{{ old('code', $institute->code ?? '') }}" required>
    <p class="hint">A short unique code, for example ICT.</p>

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $institute->name ?? '') }}" required>

    <button type="submit" class="btn" style="margin-top: 20px;">{{ isset($institute) ? 'Save changes' : 'Add institute' }}</button>
    <a href="{{ route('admin.institutes.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

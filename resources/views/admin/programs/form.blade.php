<form method="POST" action="{{ $action }}">
    @csrf
    @if (isset($program))
        @method('PUT')
    @endif

    <label for="institute_id">Institute</label>
    <select id="institute_id" name="institute_id" required>
        <option value="">Select an institute</option>
        @foreach ($institutes as $institute)
            <option value="{{ $institute->id }}"
                @selected((string) old('institute_id', $program->institute_id ?? '') === (string) $institute->id)>
                {{ $institute->code }} - {{ $institute->name }}
            </option>
        @endforeach
    </select>

    <label for="code">Code</label>
    <input type="text" id="code" name="code" value="{{ old('code', $program->code ?? '') }}" required>
    <p class="hint">A short unique code, for example BSIT.</p>

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $program->name ?? '') }}" required>

    <button type="submit" class="btn" style="margin-top: 20px;">{{ isset($program) ? 'Save changes' : 'Add program' }}</button>
    <a href="{{ route('admin.programs.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

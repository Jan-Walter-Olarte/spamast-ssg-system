<form method="POST" action="{{ $action }}">
    @csrf
    @if ($user->exists)
        @method('PUT')
    @endif

    <div class="field-row">
        <div>
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
        </div>
        <div>
            <label for="middle_name">Middle name (optional)</label>
            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
        </div>
        <div>
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
        </div>
    </div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>

    <label for="role_id">Role</label>
    <select id="role_id" name="role_id" required>
        <option value="">Select a role</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id) === $role->id)>{{ $role->name }}</option>
        @endforeach
    </select>

    <div class="remember">
        {{-- The hidden 0 makes an unticked box send "0" instead of nothing --}}
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" @checked((bool) old('is_active', $user->exists ? $user->is_active : true))>
        <label for="is_active">Account is active</label>
    </div>

    <label for="password">{{ $user->exists ? 'New password (optional)' : 'Password' }}</label>
    <input type="password" id="password" name="password" @required(! $user->exists)>
    @if ($user->exists)
        <p class="hint">Leave empty to keep the current password.</p>
    @endif

    <label for="password_confirmation">Confirm password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" @required(! $user->exists)>

    <button type="submit" class="btn" style="margin-top: 20px;">{{ $user->exists ? 'Save changes' : 'Create user' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light" style="margin-top: 20px;">Cancel</a>
</form>

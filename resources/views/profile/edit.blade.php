@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <h1>My Profile</h1>

    <div class="panel">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>

            <label for="middle_name">Middle name (optional)</label>
            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">

            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>

            <h2>Profile photo (optional)</h2>

            @if ($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="Your current photo" class="photo-preview">
            @endif

            <label for="photo">Choose a photo</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
            <p class="hint">JPG, PNG or WEBP, up to 2 MB. Choosing a new photo replaces the old one.</p>

            <h2>Change password (optional)</h2>
            <p class="hint">Leave these three boxes empty to keep your current password.</p>

            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password">

            <label for="password">New password</label>
            <input type="password" id="password" name="password">

            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">

            <button type="submit" class="btn">Save changes</button>
        </form>
    </div>
@endsection

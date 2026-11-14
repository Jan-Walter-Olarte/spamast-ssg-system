<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set a New Password | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <h1>SPAMAST <span>SSG</span></h1>
        <p class="auth-sub">Set a new password</p>

        @if (auth()->user()->must_change_password)
            <div class="alert alert-warning">
                You are using a temporary password. Please choose your own password to continue.
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf
            @method('PUT')

            <label for="current_password">Current (temporary) password</label>
            <input type="password" id="current_password" name="current_password" required autofocus>

            <label for="password">New password</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <p class="hint">At least 8 characters, and different from the current password.</p>

            <button type="submit" class="btn btn-block">Save new password</button>
        </form>

        <div class="auth-links">
            {{-- Anyone may leave the page, but a flagged user cannot go anywhere else until the password is changed --}}
            <form method="POST" action="{{ route('logout') }}" class="inline-form">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">Log out</button>
            </form>

            @unless (auth()->user()->must_change_password)
                <br><br>
                <a href="{{ route(auth()->user()->dashboardRoute()) }}">Back to my dashboard</a>
            @endunless
        </div>
    </div>
</body>
</html>

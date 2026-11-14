<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <h1>SPAMAST <span>SSG</span></h1>
        <p class="auth-sub">Choose a new password</p>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            {{-- The secret token from the link travels with the form --}}
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>

            <label for="password">New password</label>
            <input type="password" id="password" name="password" required autofocus>

            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <p class="hint">At least 8 characters.</p>

            <button type="submit" class="btn btn-block">Reset password</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </div>
</body>
</html>

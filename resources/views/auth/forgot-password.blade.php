<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <h1>SPAMAST <span>SSG</span></h1>
        <p class="auth-sub">Forgot your password?</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <p class="hint">Enter your email address and we will send you a link to choose a new password.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <button type="submit" class="btn btn-block">Send reset link</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </div>
</body>
</html>

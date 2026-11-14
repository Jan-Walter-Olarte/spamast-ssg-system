<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <h1>SPAMAST <span>SSG</span></h1>
        <p class="auth-sub">Log in to your account</p>

        {{-- Success message (for example after registering) --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-block">Login</button>

            <p class="auth-links" style="margin-top: 12px;">
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            </p>
        </form>

        <div class="auth-links">
            No account yet? <a href="{{ route('register') }}">Register</a><br><br>
            <a href="{{ route('landing') }}">Back to home</a>
        </div>
    </div>
</body>
</html>

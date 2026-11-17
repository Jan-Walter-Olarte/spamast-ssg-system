<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <div class="auth-logo"><span class="brand-mark">S</span></div>
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
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <label class="check-line"><input type="checkbox" id="show-password"> Show password</label>
            <label class="check-line"><input type="checkbox" id="remember" name="remember" value="1"> Remember me on this computer</label>

            <button type="submit" class="btn btn-block">Log in</button>

            <p class="auth-links" style="margin-top: 12px;">
                <a href="{{ route('password.request') }}">Forgot your password?</a>
            </p>
        </form>

        <div class="auth-links">
            New here? <a href="{{ route('register') }}">Create an account</a><br><br>
            <a href="{{ route('landing') }}">Back to the home page</a>
        </div>
    </div>

    <script>
        (function () {
            var toggle = document.getElementById('show-password');

            toggle.addEventListener('change', function () {
                ['password', 'password_confirmation'].forEach(function (id) {
                    var field = document.getElementById(id);
                    if (field) { field.type = toggle.checked ? 'text' : 'password'; }
                });
            });
        })();
    </script>
</body>
</html>

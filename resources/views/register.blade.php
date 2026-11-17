<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <div class="auth-logo"><span class="brand-mark">S</span></div>
        <h1>SPAMAST <span>SSG</span></h1>
        <p class="auth-sub">Create your account</p>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name">

            <label for="middle_name">Middle name (optional)</label>
            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name">

            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <p class="hint">At least 8 characters.</p>

            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">

            <label class="check-line"><input type="checkbox" id="show-password"> Show passwords</label>

            <p class="hint" style="margin-top: 14px;">New accounts are Student accounts. An Admin must approve yours before you can log in.</p>

            <button type="submit" class="btn btn-block">Create account</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}">Log in</a><br><br>
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

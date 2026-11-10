<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-box">
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
            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>

            <label for="middle_name">Middle name (optional)</label>
            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}">

            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <p class="hint">New accounts are Student accounts and must be approved by an Admin before you can log in.</p>

            <button type="submit" class="btn btn-block">Register</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="{{ route('login') }}">Login</a><br><br>
            <a href="{{ route('landing') }}">Back to home</a>
        </div>
    </div>
</body>
</html>

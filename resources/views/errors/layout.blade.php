<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') @yield('title') | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body class="auth-body">@include('partials.theme-picker')
    <div class="auth-box">
        <div class="error-code">@yield('code')</div>
        <h1 class="error-title">@yield('title')</h1>
        <p class="auth-sub">@yield('message')</p>

        {{-- /main sends a logged-in user to their own dashboard, and a visitor to the login page --}}
        <a href="{{ route('main') }}" class="btn btn-block">Back to the system</a>

        <div class="auth-links">
            <a href="{{ route('landing') }}">Home page</a>
        </div>
    </div>
</body>
</html>

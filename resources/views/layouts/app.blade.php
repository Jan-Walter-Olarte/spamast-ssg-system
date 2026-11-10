<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app">

    @include('partials.sidebar')

    <div class="content">
        <!-- Top bar with the logged-in person -->
        <header class="topbar">
            <div class="user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}</div>
                <div>
                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    <small>{{ auth()->user()->role->name }}</small>
                </div>
            </div>

            <!-- Logout is a POST form with @csrf, not a link -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        </header>

        <main class="page">
            {{-- Flash message area --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

</div>
</body>
</html>

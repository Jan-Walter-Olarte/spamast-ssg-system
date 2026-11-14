<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body>
<div class="app">

    @include('partials.sidebar')

    <div class="content">
        <!-- Top bar with the logged-in person -->
        <header class="topbar">
            <div class="user">
                <div class="avatar">
                    @if (auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="" class="avatar-img">
                    @else
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    {{ auth()->user()->full_name }}
                    <small>{{ auth()->user()->role->name }}</small>
                </div>
            </div>

            <div class="topbar-tools">
                @include('partials.notification-bell')
                @include('partials.theme-picker')

                <!-- Logout is a POST form with @csrf, not a link -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Logout</button>
                </form>
            </div>
        </header>

        <main class="page">
            {{-- Flash message area: each alert can be closed --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error alert-dismissible">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</div>
</body>
</html>

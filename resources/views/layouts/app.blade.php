<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SPAMAST SSG</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body>
<div class="app" id="app">

    {{-- On small screens, pressing the dark area closes the menu --}}
    <div class="menu-overlay" id="menu-overlay"></div>

    @include('partials.sidebar')

    <div class="content">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="tool-btn menu-btn" id="menu-btn" aria-label="Open the menu" aria-expanded="false">
                    @include('partials.icon', ['name' => 'menu'])
                    <span class="tool-label">Menu</span>
                </button>

                <div class="user">
                    <div class="avatar">
                        @if (auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="" class="avatar-img">
                        @else
                            {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-name-wrap">
                        <div class="user-name">{{ auth()->user()->full_name }}</div>
                        <small>{{ auth()->user()->role->name }}</small>
                    </div>
                </div>
            </div>

            <div class="topbar-tools">
                <button type="button" class="tool-btn" id="help-btn" title="Where do I click?">
                    @include('partials.icon', ['name' => 'help'])
                    <span class="tool-label">Help</span>
                </button>

                @include('partials.notification-bell')
                @include('partials.theme-picker')

                {{-- Logout is a POST form with @csrf, not a link --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Log out</button>
                </form>
            </div>
        </header>

        <main class="page">
            @include('partials.breadcrumbs')

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

@include('partials.help')

<script>
    (function () {
        var app = document.querySelector('.app');
        var menuButton = document.getElementById('menu-btn');
        var overlay = document.getElementById('menu-overlay');

        function closeMenu() {
            app.classList.remove('menu-open');
            menuButton.setAttribute('aria-expanded', 'false');
        }

        menuButton.addEventListener('click', function () {
            var open = app.classList.toggle('menu-open');
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        overlay.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { closeMenu(); }
        });

        var helpButton = document.getElementById('help-btn');
        var helpDialog = document.getElementById('help-dialog');

        if (helpButton && helpDialog) {
            helpButton.addEventListener('click', function () {
                if (helpDialog.showModal) { helpDialog.showModal(); } else { helpDialog.setAttribute('open', ''); }
            });
            // Clicking the dark area outside the window closes it.
            helpDialog.addEventListener('click', function (event) {
                if (event.target === helpDialog) { helpDialog.close(); }
            });
        }
    })();
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SPAMAST SSG</title>
    <style>
        :root { --green: #14532d; --green-light: #166534; --gold: #eab308; --cream: #fefce8; --text: #1f2937; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: var(--text); background: var(--cream); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 230px; background: var(--green); color: #fff; padding: 20px 0; flex-shrink: 0; }
        .sidebar .brand { padding: 0 20px 20px; font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar .brand span { color: var(--gold); }
        .sidebar a { display: block; color: #fff; text-decoration: none; padding: 12px 20px; }
        .sidebar a:hover, .sidebar a.active { background: var(--green-light); border-left: 4px solid var(--gold); }

        /* Top bar */
        .content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: #fff; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .user { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--gold); color: var(--green); font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .user small { display: block; color: #6b7280; }
        .logout { background: var(--green); color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .logout:hover { background: var(--green-light); }

        /* Page area */
        .page { padding: 30px; }
        .page h1 { color: var(--green); margin-bottom: 4px; }
        .page p { margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; }
        .card { background: #fff; border-top: 5px solid var(--gold); border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card .num { font-size: 2rem; font-weight: bold; color: var(--green); }

        /* Forms and panels (used by the profile page and later pages) */
        .panel { background: #fff; border-top: 5px solid var(--gold); border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); max-width: 640px; }
        label { display: block; font-weight: bold; margin: 12px 0 4px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #9ca3af; border-radius: 6px; font-size: 1rem; }
        .hint { font-size: 0.85rem; color: #6b7280; margin-top: 4px; }
        h2 { color: var(--green); margin: 22px 0 4px; font-size: 1.1rem; }
        .save { width: auto; margin-top: 20px; padding: 12px 24px; background: var(--green); color: #fff; border: 0; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .save:hover { background: var(--green-light); }

        /* Messages */
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

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
                <button type="submit" class="logout">Logout</button>
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

</body>
</html>

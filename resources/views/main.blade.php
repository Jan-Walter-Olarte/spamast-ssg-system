<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SPAMAST SSG</title>
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

        /* Main area */
        .content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .user { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--gold); color: var(--green); font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .user small { display: block; color: #6b7280; }
        .logout { background: var(--green); color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .logout:hover { background: var(--green-light); }

        .page { padding: 30px; }
        .page h1 { color: var(--green); margin-bottom: 4px; }
        .page p { margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; }
        .card { background: #fff; border-top: 5px solid var(--gold); border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card .num { font-size: 2rem; font-weight: bold; color: var(--green); }
    </style>
</head>
<body>

    <!-- Sidebar (links are # for now; we connect them in later steps) -->
    <aside class="sidebar">
        <div class="brand">SPAMAST <span>SSG</span></div>
        <a href="#" class="active">Dashboard</a>
        <a href="#">Events</a>
        <a href="#">Announcements</a>
        <a href="{{ route('main') }}">Refresh</a>
    </aside>

    <div class="content">
        <!-- Top bar with the logged-in person -->
        <header class="topbar">
            <div class="user">
                <div class="avatar">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</div>
                <div>
                    {{ $user->first_name }} {{ $user->last_name }}
                    <small>{{ $user->role->name }}</small>
                </div>
            </div>

            <!-- Logout must be a POST form with @csrf -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">Logout</button>
            </form>
        </header>

        <main class="page">
            <h1>Welcome, {{ $user->first_name }}!</h1>
            <p>You are logged in as <strong>{{ $user->role->name }}</strong>.</p>

            <!-- Sample numbers only; real data comes in Stage B -->
            <div class="grid">
                <div class="card"><div class="num">0</div>Upcoming events</div>
                <div class="card"><div class="num">0</div>Announcements</div>
                <div class="card"><div class="num">0</div>Attendance records</div>
            </div>
        </main>
    </div>

</body>
</html>

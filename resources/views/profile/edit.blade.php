<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SPAMAST SSG</title>
    <style>
        :root { --green: #14532d; --green-light: #166534; --gold: #eab308; --cream: #fefce8; --text: #1f2937; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: var(--text); background: var(--cream); display: flex; min-height: 100vh; }

        .sidebar { width: 230px; background: var(--green); color: #fff; padding: 20px 0; flex-shrink: 0; }
        .sidebar .brand { padding: 0 20px 20px; font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar .brand span { color: var(--gold); }
        .sidebar a { display: block; color: #fff; text-decoration: none; padding: 12px 20px; }
        .sidebar a:hover, .sidebar a.active { background: var(--green-light); border-left: 4px solid var(--gold); }

        .content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .user { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--gold); color: var(--green); font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .user small { display: block; color: #6b7280; }
        .logout { background: var(--green); color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .logout:hover { background: var(--green-light); }

        .page { padding: 30px; max-width: 640px; }
        .page h1 { color: var(--green); margin-bottom: 20px; }
        .panel { background: #fff; border-top: 5px solid var(--gold); border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        label { display: block; font-weight: bold; margin: 12px 0 4px; }
        input { width: 100%; padding: 10px; border: 1px solid #9ca3af; border-radius: 6px; font-size: 1rem; }
        .hint { font-size: 0.85rem; color: #6b7280; margin-top: 4px; }
        h2 { color: var(--green); margin: 22px 0 4px; font-size: 1.1rem; }
        .save { margin-top: 20px; padding: 12px 24px; background: var(--green); color: #fff; border: 0; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .save:hover { background: var(--green-light); }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">SPAMAST <span>SSG</span></div>
        <a href="{{ route('main') }}">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="active">My Profile</a>
    </aside>

    <div class="content">
        <header class="topbar">
            <div class="user">
                <div class="avatar">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</div>
                <div>
                    {{ $user->first_name }} {{ $user->last_name }}
                    <small>{{ $user->role->name }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">Logout</button>
            </form>
        </header>

        <main class="page">
            <h1>My Profile</h1>

            <div class="panel">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>

                    <label for="middle_name">Middle name (optional)</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">

                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>

                    <h2>Change password (optional)</h2>
                    <p class="hint">Leave these three boxes empty to keep your current password.</p>

                    <label for="current_password">Current password</label>
                    <input type="password" id="current_password" name="current_password">

                    <label for="password">New password</label>
                    <input type="password" id="password" name="password">

                    <label for="password_confirmation">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">

                    <button type="submit" class="save">Save changes</button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>

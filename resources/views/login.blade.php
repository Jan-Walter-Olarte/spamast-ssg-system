<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SPAMAST SSG</title>
    <style>
        :root { --green: #14532d; --green-light: #166534; --gold: #eab308; --cream: #fefce8; --text: #1f2937; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: var(--text); background: var(--cream); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .box { background: #fff; width: 100%; max-width: 400px; border-top: 6px solid var(--gold); border-radius: 10px; padding: 30px; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
        h1 { color: var(--green); text-align: center; margin-bottom: 4px; }
        .sub { text-align: center; margin-bottom: 20px; font-size: 0.95rem; }
        label { display: block; font-weight: bold; margin: 12px 0 4px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #9ca3af; border-radius: 6px; font-size: 1rem; }
        .remember { margin-top: 12px; }
        .remember label { display: inline; font-weight: normal; }
        button { width: 100%; margin-top: 18px; padding: 12px; background: var(--green); color: #fff; border: 0; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; }
        button:hover { background: var(--green-light); }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
        .links { text-align: center; margin-top: 16px; font-size: 0.95rem; }
        .links a { color: var(--green); font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h1>SPAMAST SSG</h1>
        <p class="sub">Log in to your account</p>

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

        {{-- action is # for now; we connect the real route in step 3.9 --}}
        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="links">
            No account yet? <a href="{{ route('register') }}">Register</a>
            <a href="{{ route('landing') }}">Back to home</a>
        </div>
    </div>
</body>
</html>

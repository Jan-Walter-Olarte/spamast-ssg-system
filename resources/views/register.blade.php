<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SPAMAST SSG</title>
    <style>
        :root { --green: #14532d; --green-light: #166534; --gold: #eab308; --cream: #fefce8; --text: #1f2937; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: var(--text); background: var(--cream); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .box { background: #fff; width: 100%; max-width: 440px; border-top: 6px solid var(--gold); border-radius: 10px; padding: 30px; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
        h1 { color: var(--green); text-align: center; margin-bottom: 4px; }
        .sub { text-align: center; margin-bottom: 20px; font-size: 0.95rem; }
        label { display: block; font-weight: bold; margin: 12px 0 4px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #9ca3af; border-radius: 6px; font-size: 1rem; }
        button { width: 100%; margin-top: 18px; padding: 12px; background: var(--green); color: #fff; border: 0; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; }
        button:hover { background: var(--green-light); }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .links { text-align: center; margin-top: 16px; font-size: 0.95rem; }
        .links a { color: var(--green); font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h1>SPAMAST SSG</h1>
        <p class="sub">Create your account</p>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- action is # for now; we connect the real route in step 3.9 --}}
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

            <button type="submit">Register</button>
        </form>

        <div class="links">
            Already have an account? <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('landing') }}">Back to home</a>
        </div>
    </div>
</body>
</html>

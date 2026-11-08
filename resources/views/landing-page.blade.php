<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAMAST SSG Event Management System</title>
    <style>
        /* Colors live here so they are easy to change */
        :root {
            --green: #14532d;
            --green-light: #166534;
            --gold: #eab308;
            --cream: #fefce8;
            --text: #1f2937;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: var(--text); background: #fff; line-height: 1.6; }

        /* Top bar */
        .navbar { background: var(--green); padding: 14px 6%; display: flex; justify-content: space-between; align-items: center; }
        .brand { color: #fff; font-weight: bold; font-size: 1.1rem; }
        .brand span { color: var(--gold); }
        .nav-links a { text-decoration: none; margin-left: 10px; padding: 8px 18px; border-radius: 6px; font-weight: bold; }
        .btn-login { color: #fff; border: 2px solid var(--gold); }
        .btn-register { background: var(--gold); color: var(--green); border: 2px solid var(--gold); }

        /* Hero */
        .hero { background: linear-gradient(135deg, var(--green), var(--green-light)); color: #fff; text-align: center; padding: 90px 6%; }
        .hero h1 { font-size: 2.4rem; margin-bottom: 14px; }
        .hero h1 span { color: var(--gold); }
        .hero p { max-width: 640px; margin: 0 auto 28px; font-size: 1.1rem; }
        .hero a { display: inline-block; background: var(--gold); color: var(--green); padding: 12px 28px; border-radius: 8px; font-weight: bold; text-decoration: none; }

        /* Sections */
        section { padding: 60px 6%; }
        section h2 { text-align: center; color: var(--green); margin-bottom: 30px; font-size: 1.8rem; }
        .roles { background: var(--cream); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card { background: #fff; border-top: 5px solid var(--gold); border-radius: 8px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card h3 { color: var(--green); margin-bottom: 8px; }
        .steps { max-width: 700px; margin: 0 auto; }
        .steps li { margin-bottom: 12px; margin-left: 20px; }

        /* Footer */
        footer { background: var(--green); color: #fff; text-align: center; padding: 20px 6%; font-size: 0.9rem; }
    </style>
</head>
<body>

    <!-- Top bar -->
    <header class="navbar">
        <div class="brand">SPAMAST <span>SSG</span></div>
        <nav class="nav-links">
            <a href="{{ route('login') }}" class="btn-login">Login</a>
            <a href="{{ route('register') }}" class="btn-register">Register</a>
        </nav>
    </header>

    <!-- Hero -->
    <div class="hero">
        <h1>SSG <span>Event Management</span> System</h1>
        <p>Plan events, record attendance with check-in and check-out, and manage fines and announcements, all in one place.</p>
        <a href="{{ route('register') }}">Get Started</a>
    </div>

    <!-- Four roles -->
    <section class="roles">
        <h2>Built for Every Role</h2>
        <div class="grid">
            <div class="card"><h3>Admin</h3><p>Approves accounts and manages institutes and programs.</p></div>
            <div class="card"><h3>Officer</h3><p>Manages students, events, attendance, fines and announcements.</p></div>
            <div class="card"><h3>Adviser</h3><p>Views events, attendance, fines and reports per institute and program.</p></div>
            <div class="card"><h3>Student</h3><p>Views events and announcements, then checks in and out.</p></div>
        </div>
    </section>

    <!-- How it works -->
    <section>
        <h2>How It Works</h2>
        <ol class="steps">
            <li>Register an account and wait for Admin approval.</li>
            <li>Log in to your own dashboard.</li>
            <li>Check in and check out at each event.</li>
            <li>View announcements and your attendance records.</li>
        </ol>
    </section>

    <!-- Footer -->
    <footer>&copy; {{ date('Y') }} SPAMAST Supreme Student Government</footer>

</body>
</html>

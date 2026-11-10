<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAMAST SSG Event Management System</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <!-- Top bar -->
    <header class="public-nav">
        <div class="brand">SPAMAST <span>SSG</span></div>
        <nav class="nav-actions">
            <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
            <a href="{{ route('register') }}" class="btn">Register</a>
        </nav>
    </header>

    <!-- Hero -->
    <div class="hero">
        <h1>SSG <span>Event Management</span> System</h1>
        <p>Plan events, record attendance with check-in and check-out, and manage fines and announcements, all in one place.</p>
        <a href="{{ route('register') }}" class="btn">Get Started</a>
    </div>

    <!-- Four roles -->
    <section class="section">
        <h2>Built for Every Role</h2>
        <div class="grid">
            <div class="card"><h3>Admin</h3><p>Approves accounts and manages institutes and programs.</p></div>
            <div class="card"><h3>Officer</h3><p>Manages students, events, attendance, fines and announcements.</p></div>
            <div class="card"><h3>Adviser</h3><p>Views events, attendance, fines and reports per institute and program.</p></div>
            <div class="card"><h3>Student</h3><p>Views events and announcements, then checks in and out.</p></div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section section-alt">
        <h2>How It Works</h2>
        <ol class="steps">
            <li>Register an account and wait for Admin approval.</li>
            <li>Log in to your own dashboard.</li>
            <li>Check in and check out at each event.</li>
            <li>View announcements and your attendance records.</li>
        </ol>
    </section>

    <!-- Footer -->
    <footer class="site-footer">&copy; {{ date('Y') }} SPAMAST Supreme Student Government</footer>

</body>
</html>

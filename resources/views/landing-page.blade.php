<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAMAST SSG Event Management System</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @include('partials.theme-init')
</head>
<body>

    <header class="public-nav">
        <a href="{{ route('landing') }}" class="brand">
            <span class="brand-mark">S</span>
            <span>SPAMAST <span class="brand-accent">SSG</span></span>
        </a>
        <nav class="nav-actions">
            @include('partials.theme-picker')
            <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
            <a href="{{ route('register') }}" class="btn">Create account</a>
        </nav>
    </header>

    <div class="hero">
        <span class="hero-badge">Event attendance made simple</span>
        <h1>Manage events and attendance <span>in one place</span></h1>
        <p>Plan events, record check-in and check-out, and keep track of fines and announcements. Everything has its own clear place.</p>
        <div class="hero-actions">
            <a href="{{ route('register') }}" class="btn btn-lg">Create an account</a>
            <a href="{{ route('login') }}" class="btn btn-lg btn-outline">Log in</a>
        </div>
        <p class="hero-note">New accounts are approved by an Admin before first use.</p>
    </div>

    <section class="section">
        <h2>What you can do</h2>
        <p class="section-sub">Simple tools for the everyday work of a student government.</p>
        <div class="feature-grid">
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'calendar', 'size' => 24])</span>
                <h3>Plan events</h3>
                <p>Create whole-day or half-day events and see them on a monthly calendar.</p>
            </div>
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'clipboard', 'size' => 24])</span>
                <h3>Record attendance</h3>
                <p>Students check in and out with a code shown at the venue.</p>
            </div>
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'coin', 'size' => 24])</span>
                <h3>Handle fines fairly</h3>
                <p>Fines follow missed scans, and officers can add sanctions.</p>
            </div>
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'megaphone', 'size' => 24])</span>
                <h3>Share announcements</h3>
                <p>Post news with files attached, and notify everyone at once.</p>
            </div>
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'chart', 'size' => 24])</span>
                <h3>Print reports</h3>
                <p>Headcount by program and a fine summary for every event.</p>
            </div>
            <div class="card feature-card">
                <span class="quick-icon">@include('partials.icon', ['name' => 'shield', 'size' => 24])</span>
                <h3>Stay in control</h3>
                <p>Every role sees only what it needs, and changes are recorded.</p>
            </div>
        </div>
    </section>

    <section class="section section-alt">
        <h2>Built for every role</h2>
        <p class="section-sub">Each person lands on a dashboard made for their job.</p>
        <div class="feature-grid">
            <div class="card"><h3>Admin</h3><p>Approves accounts and manages institutes, programs and settings.</p></div>
            <div class="card"><h3>Officer</h3><p>Manages students, events, attendance, fines and announcements.</p></div>
            <div class="card"><h3>Adviser</h3><p>Views events, attendance, fines and reports.</p></div>
            <div class="card"><h3>Student</h3><p>Checks in and out, and sees their own attendance and fines.</p></div>
        </div>
    </section>

    <section class="section">
        <h2>How it works</h2>
        <p class="section-sub">Four steps from sign-up to your first check-in.</p>
        <div class="feature-grid">
            <div class="card"><div class="step-num">1</div><h3>Create an account</h3><p>Fill in a short form with your name and email.</p></div>
            <div class="card"><div class="step-num">2</div><h3>Wait for approval</h3><p>An Admin activates your account.</p></div>
            <div class="card"><div class="step-num">3</div><h3>Log in</h3><p>You go straight to your own dashboard.</p></div>
            <div class="card"><div class="step-num">4</div><h3>Check in</h3><p>Open an event and enter the code shown at the venue.</p></div>
        </div>
    </section>

    <footer class="site-footer">&copy; {{ now()->year }} {{ $schoolName }}</footer>

</body>
</html>

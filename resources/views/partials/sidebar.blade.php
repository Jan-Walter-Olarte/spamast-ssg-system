<aside class="sidebar">
    <div class="brand">SPAMAST <span>SSG</span></div>

    {{-- Every role --}}
    <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">My Profile</a>

    {{-- Admin only --}}
    @if (auth()->user()->hasRole('Admin'))
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manage Users</a>
        <a href="{{ route('admin.institutes.index') }}" class="{{ request()->routeIs('admin.institutes.*') ? 'active' : '' }}">Institutes</a>
        <a href="{{ route('admin.programs.index') }}" class="{{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">Programs</a>
    @endif

    {{-- Officers (Gate: manage-ssg) --}}
    @can('manage-ssg')
        <a href="#">Students</a>
        <a href="#">Events</a>
        <a href="#">Attendance</a>
        <a href="#">Fines</a>
        <a href="#">Announcements</a>
    @endcan

    {{-- Advisers view events, attendance and fines --}}
    @if (auth()->user()->hasRole('Adviser'))
        <a href="#">Events</a>
        <a href="#">Attendance</a>
        <a href="#">Fines</a>
    @endif

    {{-- Officers and Advisers (Gate: view-reports) --}}
    @can('view-reports')
        <a href="#">Reports</a>
    @endcan

    {{-- Students (Gate: use-attendance) --}}
    @can('use-attendance')
        <a href="#">Events</a>
        <a href="#">Announcements</a>
        <a href="#">Check In / Check Out</a>
    @endcan
</aside>

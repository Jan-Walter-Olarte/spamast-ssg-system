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

    {{-- Officers (Gate: manage-ssg). Attendance and Fines open from each event's page. --}}
    @can('manage-ssg')
        <a href="{{ route('officer.students.index') }}" class="{{ request()->routeIs('officer.students.*') ? 'active' : '' }}">Students</a>
        <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*', 'officer.events.*', 'attendance.*', 'penalties.*') ? 'active' : '' }}">Events</a>
        <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*', 'officer.announcements.*') ? 'active' : '' }}">Announcements</a>
    @endcan

    {{-- Advisers view events, attendance, fines and announcements --}}
    @if (auth()->user()->hasRole('Adviser'))
        <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*', 'attendance.*', 'penalties.*') ? 'active' : '' }}">Events</a>
        <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">Announcements</a>
    @endif

    {{-- Officers and Advisers (Gate: view-reports) --}}
    @can('view-reports')
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
    @endcan

    {{-- Students (Gate: use-attendance). Check-in and check-out happen on each event's page. --}}
    @can('use-attendance')
        <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}">Events &amp; Check-in</a>
        <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">Announcements</a>
    @endcan
</aside>

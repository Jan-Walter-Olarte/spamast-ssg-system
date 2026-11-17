@php
    $crumbUser = auth()->user();

    // Where each part of the system lives: [name, its main page, the routes that belong to it]
    $sections = [
        ['Manage Users',   'admin.users.index',      ['admin.users.*']],
        ['Institutes',     'admin.institutes.index', ['admin.institutes.*']],
        ['Programs',       'admin.programs.index',   ['admin.programs.*']],
        ['Audit Log',      'admin.audit-log.index',  ['admin.audit-log.*']],
        ['Settings',       'admin.settings.edit',    ['admin.settings.*']],
        ['Students',       'officer.students.index', ['officer.students.*']],
        ['Events',         'events.index',           ['events.*', 'officer.events.*', 'attendance.*', 'penalties.*']],
        ['Calendar',       'calendar.index',         ['calendar.*']],
        ['Announcements',  'announcements.index',    ['announcements.*', 'officer.announcements.*']],
        ['Reports',        'reports.index',          ['reports.*']],
        ['My Attendance',  'student.my-attendance',  ['student.my-attendance']],
        ['My Fines',       'student.my-fines',       ['student.my-fines']],
        ['My Profile',     'profile.edit',           ['profile.*']],
        ['Notifications',  'notifications.index',    ['notifications.*']],
    ];

    $section       = collect($sections)->first(fn ($item) => request()->routeIs(...$item[2]));
    $pageTitle     = html_entity_decode(trim($__env->yieldContent('title')));
    $isHome        = request()->routeIs('*.dashboard');
    $isSectionPage = $section && $pageTitle === $section[0];
@endphp

@unless ($isHome)
    <nav class="breadcrumbs" aria-label="You are here">
        <a href="{{ route($crumbUser->dashboardRoute()) }}">Dashboard</a>

        @if ($section)
            <span class="sep" aria-hidden="true">&rsaquo;</span>
            @if ($isSectionPage || $pageTitle === '')
                <span aria-current="page">{{ $section[0] }}</span>
            @else
                <a href="{{ route($section[1]) }}">{{ $section[0] }}</a>
            @endif
        @endif

        @if ($pageTitle !== '' && ! $isSectionPage)
            <span class="sep" aria-hidden="true">&rsaquo;</span>
            <span aria-current="page">{{ $pageTitle }}</span>
        @endif
    </nav>
@endunless

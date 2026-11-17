@php
    $sideUser = auth()->user();

    // Each group: [heading, [ [label, route, routes that count as "here", icon], ... ]]
    $groups = [
        [null, [['Dashboard', $sideUser->dashboardRoute(), ['*.dashboard'], 'home']]],
    ];

    if ($sideUser->hasRole('Admin')) {
        $groups[] = ['Manage', [
            ['Manage Users', 'admin.users.index',      ['admin.users.*'],      'users'],
            ['Institutes',   'admin.institutes.index', ['admin.institutes.*'], 'building'],
            ['Programs',     'admin.programs.index',   ['admin.programs.*'],   'book'],
        ]];
        $groups[] = ['System', [
            ['Audit Log', 'admin.audit-log.index', ['admin.audit-log.*'], 'shield'],
            ['Settings',  'admin.settings.edit',   ['admin.settings.*'],  'sliders'],
        ]];
    }

    if ($sideUser->hasRole('Officer')) {
        $groups[] = ['Manage', [
            ['Students',      'officer.students.index', ['officer.students.*'],                                                'graduation'],
            ['Events',        'events.index',           ['events.*', 'officer.events.*', 'attendance.*', 'penalties.*'],       'flag'],
            ['Calendar',      'calendar.index',         ['calendar.*'],                                                        'calendar'],
            ['Announcements', 'announcements.index',    ['announcements.*', 'officer.announcements.*'],                        'megaphone'],
        ]];
        $groups[] = ['Insights', [
            ['Reports', 'reports.index', ['reports.*'], 'chart'],
        ]];
    }

    if ($sideUser->hasRole('Adviser')) {
        $groups[] = ['Browse', [
            ['Events',        'events.index',        ['events.*', 'attendance.*', 'penalties.*'], 'flag'],
            ['Calendar',      'calendar.index',      ['calendar.*'],                              'calendar'],
            ['Announcements', 'announcements.index', ['announcements.*'],                         'megaphone'],
        ]];
        $groups[] = ['Insights', [
            ['Reports', 'reports.index', ['reports.*'], 'chart'],
        ]];
    }

    if ($sideUser->hasRole('Student')) {
        $groups[] = ['My Activity', [
            ['Events & Check-in', 'events.index',           ['events.*'],              'flag'],
            ['My Attendance',     'student.my-attendance',  ['student.my-attendance'], 'clipboard'],
            ['My Fines',          'student.my-fines',       ['student.my-fines'],      'coin'],
        ]];
        $groups[] = ['Browse', [
            ['Calendar',      'calendar.index',      ['calendar.*'],      'calendar'],
            ['Announcements', 'announcements.index', ['announcements.*'], 'megaphone'],
        ]];
    }

    $groups[] = ['Account', [
        ['My Profile',     'profile.edit',        ['profile.*'],       'user'],
        ['Notifications',  'notifications.index', ['notifications.*'], 'bell'],
    ]];

    $unreadCount = $sideUser->unreadNotifications()->count();
@endphp

<aside class="sidebar" aria-label="Main menu">
    <a href="{{ route($sideUser->dashboardRoute()) }}" class="brand">
        <span class="brand-mark">S</span>
        <span>SPAMAST <span class="brand-accent">SSG</span></span>
    </a>

    @foreach ($groups as [$heading, $items])
        @if ($heading)
            <div class="side-title">{{ $heading }}</div>
        @endif

        @foreach ($items as [$label, $route, $patterns, $icon])
            <a href="{{ route($route) }}" class="side-link {{ request()->routeIs(...$patterns) ? 'active' : '' }}"
               @if (request()->routeIs(...$patterns)) aria-current="page" @endif>
                @include('partials.icon', ['name' => $icon])
                <span>{{ $label }}</span>
                @if ($label === 'Notifications' && $unreadCount > 0)
                    <span class="side-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
        @endforeach
    @endforeach
</aside>

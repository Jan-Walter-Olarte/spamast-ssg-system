@php
    $quickUser = auth()->user();

    // Each action: [title, short hint, route, icon]
    if ($quickUser->hasRole('Admin')) {
        $actions = [
            ['Review new accounts', 'Approve people who signed up',     'admin.users.index',      'user-check'],
            ['Add a user',          'Create an Officer, Adviser or Student', 'admin.users.create', 'user-plus'],
            ['Institutes',          'Manage the school structure',      'admin.institutes.index', 'building'],
            ['Programs',            'Courses inside each institute',    'admin.programs.index',   'book'],
            ['Audit log',           'See who changed what',             'admin.audit-log.index',  'shield'],
            ['Settings',            'School name and default fine',     'admin.settings.edit',    'sliders'],
        ];
    } elseif ($quickUser->hasRole('Officer')) {
        $actions = [
            ['Add a student',       'Register one student',             'officer.students.create',      'user-plus'],
            ['Import students',     'Add many students from a CSV file', 'officer.students.import',     'graduation'],
            ['Create an event',     'Whole day or half day',            'officer.events.create',        'flag'],
            ['Post an announcement', 'Share news with everyone',        'officer.announcements.create', 'megaphone'],
            ['Open the calendar',   'See events by month',              'calendar.index',               'calendar'],
            ['View reports',        'Headcount and fines per event',    'reports.index',                'chart'],
        ];
    } elseif ($quickUser->hasRole('Adviser')) {
        $actions = [
            ['Browse events',       'Attendance and fines per event',   'events.index',        'flag'],
            ['Open the calendar',   'See events by month',              'calendar.index',      'calendar'],
            ['View reports',        'Print an event report',            'reports.index',       'chart'],
            ['Read announcements',  'The latest news',                  'announcements.index', 'megaphone'],
        ];
    } else {
        $actions = [
            ['Events & Check-in',   'Find an event and check in',       'events.index',          'flag'],
            ['My attendance',       'Your check-ins and check-outs',    'student.my-attendance', 'clipboard'],
            ['My fines',            'What you owe, if anything',        'student.my-fines',      'coin'],
            ['Open the calendar',   'See upcoming events',              'calendar.index',        'calendar'],
            ['Read announcements',  'The latest news',                  'announcements.index',   'megaphone'],
        ];
    }
@endphp

<h2>Quick actions</h2>
<div class="quick-grid">
    @foreach ($actions as [$actionTitle, $actionHint, $actionRoute, $actionIcon])
        <a href="{{ route($actionRoute) }}" class="quick-card">
            <span class="quick-icon">@include('partials.icon', ['name' => $actionIcon, 'size' => 24])</span>
            <span>
                <span class="quick-title">{{ $actionTitle }}</span>
                <span class="quick-hint">{{ $actionHint }}</span>
            </span>
        </a>
    @endforeach
</div>

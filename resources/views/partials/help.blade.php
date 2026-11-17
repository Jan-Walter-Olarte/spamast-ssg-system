@php($helpUser = auth()->user())

<dialog class="help-dialog" id="help-dialog" aria-labelledby="help-title">
    <div class="help-body">
        <h2 id="help-title" style="margin-top: 0;">Need help finding something?</h2>
        <p class="hint" style="margin-bottom: 10px;">The menu on the left is always there. Here are the most common jobs for your role, and where to click.</p>

        @if ($helpUser->hasRole('Admin'))
            <h3>Approve a new account</h3>
            <ul>
                <li>Open <strong>Manage Users</strong>, find the person, and press <strong>Activate</strong>.</li>
                <li>People waiting for approval show an amber <em>Inactive / Pending</em> label.</li>
            </ul>
            <h3>Set up the school</h3>
            <ul>
                <li>Add your <strong>Institutes</strong> first, then the <strong>Programs</strong> inside them.</li>
            </ul>
            <h3>Check what happened</h3>
            <ul>
                <li><strong>Audit Log</strong> shows who changed attendance, fines and accounts, and when.</li>
                <li><strong>Settings</strong> changes the school name and the default fine.</li>
            </ul>
        @endif

        @if ($helpUser->hasRole('Officer'))
            <h3>Add students</h3>
            <ul>
                <li>Open <strong>Students</strong>, then press <strong>Add Student</strong> for one person, or <strong>Import CSV</strong> for many.</li>
            </ul>
            <h3>Create an event</h3>
            <ul>
                <li>Open <strong>Events</strong> and press <strong>Add Event</strong>. Choose a whole day or a half day.</li>
                <li>Open the event to see its <strong>check-in code</strong>. Show it at the venue so students can check in.</li>
                <li>For a manual event, press <strong>Open check-in</strong> and <strong>Open check-out</strong> on the event page.</li>
            </ul>
            <h3>Fines and attendance</h3>
            <ul>
                <li>Open an event, then <strong>Attendance</strong> to fix times, or <strong>Fines</strong> and press <strong>Generate fines</strong> after the event.</li>
                <li>Use <strong>Announcements</strong> to share news with everyone.</li>
            </ul>
        @endif

        @if ($helpUser->hasRole('Adviser'))
            <h3>Read attendance and fines</h3>
            <ul>
                <li>Open <strong>Events</strong>, choose an event, then press <strong>Attendance</strong> or <strong>Fines</strong>.</li>
                <li>Use the filters at the top to narrow the list by institute or program.</li>
            </ul>
            <h3>Print a report</h3>
            <ul>
                <li>Open <strong>Reports</strong>, choose <strong>View report</strong>, then press <strong>Print</strong>.</li>
            </ul>
        @endif

        @if ($helpUser->hasRole('Student'))
            <h3>Check in to an event</h3>
            <ul>
                <li>Open <strong>Events &amp; Check-in</strong> in the menu and choose the event.</li>
                <li>Type the <strong>code shown at the venue</strong> next to <strong>Check in</strong> and press the button.</li>
                <li>When you leave, do the same with <strong>Check out</strong>.</li>
            </ul>
            <h3>See your records</h3>
            <ul>
                <li><strong>My Attendance</strong> lists your check-ins. <strong>My Fines</strong> shows what you owe.</li>
            </ul>
        @endif

        <h3>Change how the site looks</h3>
        <ul>
            <li>Press <strong>Theme</strong> at the top to pick a color theme.</li>
            <li>Choose <strong>Auto</strong> to match your browser or device, or force <strong>Light</strong> or <strong>Dark</strong>.</li>
        </ul>

        <h3>Forgot your password?</h3>
        <ul>
            <li>Log out, then press <strong>Forgot your password?</strong> on the login page.</li>
        </ul>
    </div>

    <form method="dialog" class="help-foot">
        <button type="submit" class="btn">Got it</button>
    </form>
</dialog>

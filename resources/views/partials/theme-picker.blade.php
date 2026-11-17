@php
    // Name and three preview colors (header, button, page) for each theme.
    $themes = [
        'cinematic' => ['Cinematic',                 ['#120805', '#F5B301', '#F5F0E6']],
        'saas'      => ['Modern Dark SaaS',          ['#1E1245', '#8B5CF6', '#F5F4FB']],
        'blue'      => ['Premium Blue Technology',   ['#0B1F44', '#2563EB', '#F4F7FB']],
        'friendly'  => ['Friendly / Anime',          ['#B0103A', '#F97316', '#FFF6EC']],
        'minimal'   => ['Minimalist Product',        ['#D9D7D0', '#34332F', '#E9E8E4']],
        'red'       => ['Futuristic Red Technology', ['#150509', '#D41F43', '#FAF4F4']],
    ];
@endphp

<div class="theme-menu" data-theme-menu>
    <button type="button" class="tool-btn theme-trigger" data-theme-trigger aria-haspopup="true" aria-expanded="false"
            title="Change the colors and the light or dark look">
        <span class="theme-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="tool-label">Theme</span>
    </button>

    <div class="theme-panel" data-theme-panel role="group" aria-label="Choose how the site looks" hidden>
        <p class="theme-heading">Color theme</p>
        <div class="theme-options">
            @foreach ($themes as $key => [$label, $colors])
                <button type="button" class="theme-option" data-theme-choice="{{ $key }}" aria-pressed="false">
                    <span class="theme-swatch" aria-hidden="true">
                        @foreach ($colors as $color)<i style="background: {{ $color }};"></i>@endforeach
                    </span>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <p class="theme-heading">Appearance</p>
        <div class="mode-options">
            <button type="button" class="mode-option" data-mode-choice="auto" aria-pressed="false">Auto</button>
            <button type="button" class="mode-option" data-mode-choice="light" aria-pressed="false">Light</button>
            <button type="button" class="mode-option" data-mode-choice="dark" aria-pressed="false">Dark</button>
        </div>
        <p class="hint">Auto follows your browser or device setting.</p>
    </div>
</div>

<script>
    (function () {
        var menu = document.querySelector('[data-theme-menu]');
        if (!menu) { return; }

        var trigger = menu.querySelector('[data-theme-trigger]');
        var panel = menu.querySelector('[data-theme-panel]');
        var root = document.documentElement;
        var look = window.spamastLook;

        // Highlight the buttons that match what is applied now.
        function mark(selector, attribute, current) {
            menu.querySelectorAll(selector).forEach(function (button) {
                button.setAttribute('aria-pressed', button.getAttribute(attribute) === current ? 'true' : 'false');
            });
        }

        function refresh() {
            mark('[data-theme-choice]', 'data-theme-choice', root.getAttribute('data-theme'));
            mark('[data-mode-choice]', 'data-mode-choice', root.getAttribute('data-mode-pref'));
        }

        function toggle(show) {
            panel.hidden = !show;
            trigger.setAttribute('aria-expanded', show ? 'true' : 'false');
            if (show) { refresh(); }
        }

        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            toggle(panel.hidden);
        });
        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) { toggle(false); }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { toggle(false); }
        });

        menu.querySelectorAll('[data-theme-choice]').forEach(function (button) {
            button.addEventListener('click', function () {
                var chosen = button.getAttribute('data-theme-choice');
                if (look) { look.setTheme(chosen); }
                refresh();

                @auth
                // Logged in: also save it on the account, so it follows the user to any computer.
                fetch(@json(route('profile.theme')), {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ theme: chosen })
                }).catch(function () { /* the theme still works on this page if saving fails */ });
                @endauth
            });
        });

        menu.querySelectorAll('[data-mode-choice]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (look) { look.setMode(button.getAttribute('data-mode-choice')); }
                refresh();
            });
        });

        refresh();
    })();
</script>

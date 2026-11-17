<script>
    (function () {
        var themes = ['cinematic', 'saas', 'blue', 'friendly', 'minimal', 'red'];
        var modes = ['auto', 'light', 'dark'];
        var root = document.documentElement;
        var theme = 'blue';
        var pref = 'auto';

        // The theme saved on the logged-in account (null for guests or when none is saved).
        var account = @json(auth()->user()?->theme);

        try {
            var savedTheme = localStorage.getItem('spamast-theme');
            if (themes.indexOf(savedTheme) !== -1) { theme = savedTheme; }

            var savedMode = localStorage.getItem('spamast-mode');
            if (modes.indexOf(savedMode) !== -1) { pref = savedMode; }
        } catch (e) { /* storage is blocked: keep the defaults */ }

        if (themes.indexOf(account) !== -1) {
            theme = account;
            try { localStorage.setItem('spamast-theme', account); } catch (e) { /* ignore */ }
        }

        // "Auto" asks the browser or device whether it is in dark mode.
        var query = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function resolve(choice) {
            if (choice === 'light' || choice === 'dark') { return choice; }
            return query && query.matches ? 'dark' : 'light';
        }

        function apply() {
            root.setAttribute('data-theme', theme);
            root.setAttribute('data-mode-pref', pref);
            root.setAttribute('data-mode', resolve(pref));
        }

        apply();

        // Used by the theme picker.
        window.spamastLook = {
            setTheme: function (value) {
                if (themes.indexOf(value) === -1) { return; }
                theme = value;
                try { localStorage.setItem('spamast-theme', value); } catch (e) { /* ignore */ }
                apply();
            },
            setMode: function (value) {
                if (modes.indexOf(value) === -1) { return; }
                pref = value;
                try { localStorage.setItem('spamast-mode', value); } catch (e) { /* ignore */ }
                apply();
            }
        };

        // If the device switches between light and dark while "Auto" is on, follow it at once.
        if (query) {
            var follow = function () { if (pref === 'auto') { apply(); } };
            if (query.addEventListener) { query.addEventListener('change', follow); }
            else if (query.addListener) { query.addListener(follow); }
        }
    })();
</script>

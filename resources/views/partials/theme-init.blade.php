<script>
    (function () {
        var allowed = ['cinematic', 'saas', 'blue', 'friendly', 'minimal', 'red'];
        var theme = 'blue';

        // The theme saved on the logged-in account (null for guests or when none is saved).
        var account = @json(auth()->user()?->theme);

        // 1. This browser's memory.
        try {
            var saved = localStorage.getItem('spamast-theme');
            if (allowed.indexOf(saved) !== -1) { theme = saved; }
        } catch (e) { /* storage is blocked: keep going */ }

        // 2. A theme saved on the account wins, and the browser remembers it too.
        if (allowed.indexOf(account) !== -1) {
            theme = account;
            try { localStorage.setItem('spamast-theme', account); } catch (e) { /* ignore */ }
        }

        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>

<script>
    (function () {
        var allowed = ['cinematic', 'saas', 'blue', 'friendly', 'minimal', 'red'];
        var theme = 'blue';

        try {
            var saved = localStorage.getItem('spamast-theme');
            if (allowed.indexOf(saved) !== -1) { theme = saved; }
        } catch (e) { /* storage is blocked: keep the default theme */ }

        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>

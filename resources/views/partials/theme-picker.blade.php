<label class="theme-picker" title="Choose a theme">
    <span class="sr-only">Theme</span>
    <select id="theme-select" aria-label="Choose a theme">
        <option value="cinematic">Cinematic</option>
        <option value="saas">Modern Dark SaaS</option>
        <option value="blue">Premium Blue Technology</option>
        <option value="friendly">Friendly / Anime</option>
        <option value="minimal">Minimalist Product</option>
        <option value="red">Futuristic Red Technology</option>
    </select>
</label>

<script>
    (function () {
        var select = document.getElementById('theme-select');
        if (!select) { return; }

        // Show the theme that is currently applied.
        select.value = document.documentElement.getAttribute('data-theme') || 'blue';

        // Apply and remember a new choice.
        select.addEventListener('change', function () {
            document.documentElement.setAttribute('data-theme', select.value);
            try { localStorage.setItem('spamast-theme', select.value); } catch (e) { /* ignore */ }
        });
    })();
</script>

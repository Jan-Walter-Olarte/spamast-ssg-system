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

        select.addEventListener('change', function () {
            // Apply it at once and remember it in this browser.
            document.documentElement.setAttribute('data-theme', select.value);
            try { localStorage.setItem('spamast-theme', select.value); } catch (e) { /* ignore */ }

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
                body: JSON.stringify({ theme: select.value })
            }).catch(function () { /* the theme still works on this page if saving fails */ });
            @endauth
        });
    })();
</script>

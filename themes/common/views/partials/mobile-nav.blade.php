{{-- Hamburger (CSS: cms-content.css) – Panel separat als mobile-nav-panel --}}
<button
    type="button"
    class="cms-mnav-toggle"
    id="cms-mnav-toggle"
    aria-expanded="false"
    aria-controls="cms-mnav-panel"
    aria-label="Menü"
>
    <span class="cms-mnav-toggle__bars" aria-hidden="true">
        <span></span><span></span><span></span>
    </span>
</button>
<script>
(function () {
    if (window.__cmsMnavBound) return;
    window.__cmsMnavBound = true;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('.cms-mnav-toggle');
        var panel = document.getElementById('cms-mnav-panel');
        if (!panel) return;
        if (btn) {
            var open = !panel.classList.contains('is-open');
            panel.classList.toggle('is-open', open);
            panel.hidden = !open;
            document.querySelectorAll('.cms-mnav-toggle').forEach(function (b) {
                b.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            return;
        }
        if (panel.classList.contains('is-open') && !panel.contains(e.target)) {
            panel.classList.remove('is-open');
            panel.hidden = true;
            document.querySelectorAll('.cms-mnav-toggle').forEach(function (b) {
                b.setAttribute('aria-expanded', 'false');
            });
        }
    });
})();
</script>

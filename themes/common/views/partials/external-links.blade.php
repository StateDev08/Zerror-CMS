{{-- Externe Links + Admin automatisch in neuem Tab öffnen --}}
<script>
(function () {
    function shouldOpenBlank(a) {
        if (!a || a.tagName !== 'A') return false;
        if (a.hasAttribute('download') || a.hasAttribute('data-same-tab')) return false;
        if (a.closest('nav, .top-nav, [data-menu]')) return false;
        if (a.target && a.target !== '' && a.target !== '_self') return true;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return false;
        var lower = href.toLowerCase();
        if (lower.indexOf('mailto:') === 0 || lower.indexOf('tel:') === 0) return false;
        if (/^[a-z][a-z0-9+.-]*:/i.test(href) && lower.indexOf('http:') !== 0 && lower.indexOf('https:') !== 0) {
            return true;
        }
        try {
            var u = new URL(href, window.location.href);
            if (u.origin !== window.location.origin) return true;
            if (/\/admin(\/|$)/.test(u.pathname)) return true;
        } catch (e) {
            return false;
        }
        return false;
    }

    function apply(root) {
        (root || document).querySelectorAll('a[href]').forEach(function (a) {
            if (!shouldOpenBlank(a)) return;
            a.setAttribute('target', '_blank');
            var rel = (a.getAttribute('rel') || '').toLowerCase();
            if (rel.indexOf('noopener') === -1) {
                a.setAttribute('rel', (rel ? rel + ' ' : '') + 'noopener noreferrer');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { apply(document); });
    } else {
        apply(document);
    }

    // Dynamisch eingefügte Widgets/Inhalte
    if (window.MutationObserver) {
        var obs = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) apply(node);
                });
            });
        });
        obs.observe(document.documentElement, { childList: true, subtree: true });
    }
})();
</script>
@include('theme::partials.cms-music-persist')

@php
    $cfg = module_config('google_translate');
    $showLabel = filter_var($cfg['show_label'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $pageLang = preg_replace('/[^a-zA-Z\-]/', '', (string) ($cfg['page_language'] ?? 'de')) ?: 'de';
    $raw = trim((string) ($cfg['included_languages'] ?? ''));
    $codes = $raw !== ''
        ? array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $raw) ?: [])))
        : ['en','fr','es','it','pl','nl','pt','ru','tr','uk','zh-CN','ja','ko'];

    $labels = [
        'de' => 'Deutsch',
        'en' => 'English',
        'fr' => 'Français',
        'es' => 'Español',
        'it' => 'Italiano',
        'pl' => 'Polski',
        'nl' => 'Nederlands',
        'pt' => 'Português',
        'ru' => 'Русский',
        'tr' => 'Türkçe',
        'uk' => 'Українська',
        'zh-CN' => '中文',
        'zh-TW' => '中文 (繁體)',
        'ja' => '日本語',
        'ko' => '한국어',
        'ar' => 'العربية',
        'cs' => 'Čeština',
        'da' => 'Dansk',
        'fi' => 'Suomi',
        'sv' => 'Svenska',
        'no' => 'Norsk',
        'hu' => 'Magyar',
        'ro' => 'Română',
        'el' => 'Ελληνικά',
        'hi' => 'हिन्दी',
        'th' => 'ไทย',
        'vi' => 'Tiếng Việt',
        'id' => 'Indonesia',
        'ms' => 'Melayu',
        'he' => 'עברית',
        'bg' => 'Български',
        'hr' => 'Hrvatski',
        'sk' => 'Slovenčina',
        'sl' => 'Slovenščina',
        'lt' => 'Lietuvių',
        'lv' => 'Latviešu',
        'et' => 'Eesti',
    ];

    $current = $pageLang;
    // Bei doppelten googtrans-Cookies den letzten gültigen Wert nehmen
    $rawCookie = (string) ($_COOKIE['googtrans'] ?? '');
    if ($rawCookie !== '' && preg_match('#/([^/]+)/([^/]+)#', $rawCookie, $m)) {
        $current = $m[2];
    }
@endphp
<div class="gt-nav notranslate" translate="no" aria-label="{{ __('widgets.google_translate_label') }}">
    @if($showLabel)
        <label class="gt-nav__label" for="gt-nav-select">{{ __('widgets.google_translate_label') }}</label>
    @endif
    <select
        id="gt-nav-select"
        class="gt-nav__select"
        data-page-lang="{{ $pageLang }}"
        aria-label="{{ __('widgets.google_translate_label') }}"
    >
        <option value="{{ $pageLang }}" @selected($current === $pageLang)>{{ $labels[$pageLang] ?? strtoupper($pageLang) }}</option>
        @foreach($codes as $code)
            @if($code === $pageLang)
                @continue
            @endif
            <option value="{{ $code }}" @selected($current === $code)>{{ $labels[$code] ?? strtoupper($code) }}</option>
        @endforeach
    </select>
    <div id="google_translate_element" class="gt-nav__engine" aria-hidden="true"></div>
</div>
<script>
(function () {
    var sel = document.getElementById('gt-nav-select');
    if (!sel || sel.dataset.bound === '1') return;
    sel.dataset.bound = '1';
    var pageLang = sel.getAttribute('data-page-lang') || 'de';

    function cookieDomains() {
        var host = location.hostname;
        var out = [null, host, '.' + host];
        var parts = host.split('.');
        if (parts.length >= 2) {
            var root = parts.slice(-2).join('.');
            out.push(root, '.' + root);
        }
        if (parts.length >= 3) {
            var mid = parts.slice(-3).join('.');
            out.push(mid, '.' + mid);
        }
        return out;
    }

    function clearGoogTrans() {
        var names = ['googtrans', 'googtrans'];
        var paths = ['/', ''];
        try { paths.push(location.pathname || '/'); } catch (e) {}
        cookieDomains().forEach(function (domain) {
            paths.forEach(function (path) {
                var base = 'googtrans=;expires=Thu, 01 Jan 1970 00:00:00 GMT;Max-Age=0;path=' + (path || '/') + ';SameSite=Lax';
                document.cookie = base;
                document.cookie = base + ';Secure';
                if (domain) {
                    document.cookie = base + ';domain=' + domain;
                    document.cookie = base + ';domain=' + domain + ';Secure';
                }
            });
        });
        try {
            if (location.hash && /googtrans/i.test(location.hash)) {
                history.replaceState(null, '', location.pathname + location.search);
            }
        } catch (e) {}
    }

    function setGoogTrans(value) {
        // Immer zuerst alle Varianten löschen – sonst bleiben Doppel-Cookies (Host + Domain)
        // und Google springt nach dem 2. Wechsel wieder auf die alte Sprache zurück.
        clearGoogTrans();
        // Nur EIN Host-Cookie setzen (kein domain=…)
        document.cookie = 'googtrans=' + value + ';path=/;max-age=31536000;SameSite=Lax';
    }

    sel.addEventListener('change', function () {
        var to = sel.value || pageLang;
        if (to === pageLang) {
            clearGoogTrans();
        } else {
            setGoogTrans('/' + pageLang + '/' + to);
        }
        // Hard-Reload ohne Turbo-Cache
        try {
            if (window.Turbo && typeof Turbo.visit === 'function') {
                Turbo.visit(location.pathname + location.search, { action: 'replace' });
                setTimeout(function () { location.reload(); }, 50);
                return;
            }
        } catch (e) {}
        location.reload();
    });
})();
</script>

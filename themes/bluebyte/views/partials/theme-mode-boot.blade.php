{{-- Site-Default aus Installation/Admin; Cookie nur nach bewusster User-Wahl --}}
@php
    $themeManager = app(\App\Support\ThemeManager::class);
    $zcThemeDefaultMode = $themeManager->getDefaultThemeMode();
    $zcThemeModeRevision = $themeManager->getThemeModeRevision();
@endphp
<script>
(function () {
    var defaultMode = @json($zcThemeDefaultMode);
    var revision = String(@json($zcThemeModeRevision));
    var cookies = document.cookie.split('; ').reduce(function (acc, part) {
        var i = part.indexOf('=');
        if (i > 0) acc[part.slice(0, i)] = decodeURIComponent(part.slice(i + 1));
        return acc;
    }, {});
    var userChoice = cookies.zerrocms_theme_user_choice === '1';
    var cookieRev = cookies.zerrocms_theme_mode_rev || '';
    var cookieMode = cookies.zerrocms_theme_mode || '';
    var mode = defaultMode;
    if (userChoice && cookieRev === revision && (cookieMode === 'dark' || cookieMode === 'light' || cookieMode === 'system')) {
        mode = cookieMode;
    }
    function apply(modeName) {
        var dark = false;
        if (modeName === 'dark') dark = true;
        else if (modeName === 'light') dark = false;
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) dark = true;
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.setAttribute('data-theme-mode', modeName);
    }
    apply(mode);
    window.zerrocmsThemeMode = {
        revision: revision,
        defaultMode: defaultMode,
        apply: apply,
        setUserMode: function (modeName) {
            if (modeName !== 'dark' && modeName !== 'light' && modeName !== 'system') return;
            var maxAge = 31536000;
            document.cookie = 'zerrocms_theme_mode=' + encodeURIComponent(modeName) + ';path=/;max-age=' + maxAge + ';SameSite=Lax';
            document.cookie = 'zerrocms_theme_user_choice=1;path=/;max-age=' + maxAge + ';SameSite=Lax';
            document.cookie = 'zerrocms_theme_mode_rev=' + encodeURIComponent(revision) + ';path=/;max-age=' + maxAge + ';SameSite=Lax';
            apply(modeName);
        },
        toggle: function () {
            var isDark = document.documentElement.classList.contains('dark');
            this.setUserMode(isDark ? 'light' : 'dark');
        }
    };
})();
</script>

{{-- Cookie-Banner aus ACP (Site Settings → Cookies). Globale SSOT für alle Themes. --}}
@php
    use App\Support\SiteContent;
    $consentRaw = request()->cookie('zerrocms_cookie_consent');
    $hasConsent = $consentRaw === '1' || $consentRaw === 1 || $consentRaw === true;
@endphp
@if(SiteContent::cookieBannerEnabled() && ! $hasConsent)
    <div id="cookie-consent" class="fixed bottom-0 left-0 right-0 bg-neutral-900/95 dark:bg-neutral-950/95 backdrop-blur-md text-neutral-200 px-6 py-4 text-center text-sm z-50 flex flex-wrap items-center justify-center gap-3 rounded-t-2xl shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.25)]" role="dialog" aria-label="{{ __('cookie_consent.banner_label') }}" hidden>
        <span>{{ SiteContent::cookieBannerText() }}</span>
        <a href="{{ route('page.show', ['slug' => 'cookies']) }}" class="theme-link-primary underline">{{ SiteContent::cookieBannerLinkLabel() }}</a>
        <button type="button" id="cookie-consent-accept" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl text-sm transition-colors">{{ SiteContent::cookieBannerButton() }}</button>
    </div>
    <script>
    (function () {
        var key = 'zerrocms_cookie_consent';
        var el = document.getElementById('cookie-consent');
        if (!el) return;
        var accepted = document.cookie.split(';').some(function (c) {
            return c.trim().indexOf(key + '=') === 0;
        });
        if (accepted) {
            el.remove();
            return;
        }
        el.hidden = false;
        var btn = document.getElementById('cookie-consent-accept');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var secure = location.protocol === 'https:' ? ';Secure' : '';
            document.cookie = key + '=1;path=/;max-age=31536000;SameSite=Lax' + secure;
            el.remove();
        });
    })();
    </script>
@endif

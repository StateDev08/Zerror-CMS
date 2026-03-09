<div id="zerrocms-cookie-consent" class="fixed bottom-0 left-0 right-0 z-50 bg-neutral-900/95 dark:bg-neutral-950/95 backdrop-blur-md border-t border-neutral-700/50 p-4 md:p-5 rounded-t-2xl shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.25)]" role="dialog" aria-label="{{ __('cookie_consent.banner_label', [], 'Cookie-Hinweis') }}" style="display: none;">
    <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-neutral-200">{{ $banner_text }}</p>
        <div class="flex items-center gap-3">
            @if(!empty($privacy_url))
                <a href="{{ $privacy_url }}" class="text-sm text-neutral-400 hover:text-white transition-colors">{{ __('nav.datenschutz', [], 'Datenschutz') }}</a>
            @endif
            <button type="button" id="zerrocms-cookie-consent-accept" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition-colors">{{ $accept_button }}</button>
        </div>
    </div>
</div>
<script>
(function(){
    var key = 'zerrocms_cookie_consent';
    var accepted = document.cookie.split(';').some(function(c){ return c.trim().indexOf(key + '=') === 0; });
    var el = document.getElementById('zerrocms-cookie-consent');
    if (!el) return;
    if (accepted) return;
    el.style.display = 'block';
    document.getElementById('zerrocms-cookie-consent-accept').addEventListener('click', function(){
        document.cookie = key + '=1; path=/; max-age=31536000; SameSite=Lax';
        el.style.display = 'none';
    });
})();
</script>

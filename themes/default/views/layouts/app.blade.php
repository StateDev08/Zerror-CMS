<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName = setting('site_name', config('clan.name'));
        $seoTitle = setting('seo_default_title', $siteName);
        $seoDescription = setting('seo_default_description', '');
        $seoOgImage = setting('seo_og_image', '');
    @endphp
    <title>@yield('title', $seoTitle)</title>
    @if($seoDescription)
        <meta name="description" content="{{ e($seoDescription) }}">
    @endif
    <meta property="og:title" content="{{ e($seoTitle) }}">
    @if($seoDescription)
        <meta property="og:description" content="{{ e($seoDescription) }}">
    @endif
    @if($seoOgImage)
        <meta property="og:image" content="{{ e($seoOgImage) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @php
        $themeManager = app(\App\Support\ThemeManager::class);
        $themeColors = $themeManager->getThemeColors();
        $layoutOptions = $themeManager->getLayoutOptions();
        $navRight = ($layoutOptions['nav_sidebar_position'] ?? 'left') === 'right';
        $mainOrderWidgetsFirst = ($layoutOptions['main_order'] ?? 'content_first') === 'widgets_first';
    @endphp
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] }};
            --theme-accent: {{ $themeColors['accent'] }};
            --theme-background: {{ $themeColors['background'] }};
            --theme-surface: {{ $themeColors['surface'] }};
            --theme-text: {{ $themeColors['text'] }};
            --theme-text-muted: {{ $themeColors['text_muted'] }};
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.25rem;
            --radius-2xl: 1.5rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.08), 0 8px 10px -6px rgb(0 0 0 / 0.04);
        }
        .dark { --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.2); --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.2), 0 2px 4px -2px rgb(0 0 0 / 0.15); --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.2), 0 4px 6px -4px rgb(0 0 0 / 0.15); }
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .theme-link-primary { color: var(--theme-primary); transition: color 0.15s ease, opacity 0.15s ease; }
        .theme-link-primary:hover { opacity: 0.85; text-decoration: underline; }
        .theme-bg-primary { background-color: var(--theme-primary); }
        .theme-bg-accent { background-color: var(--theme-accent); }
        #site-sidebar .theme-nav-active { border-left-color: var(--theme-primary); color: var(--theme-primary); font-weight: 600; }
        .widget { border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); transition: box-shadow 0.2s ease; }
        .widget:hover { box-shadow: var(--shadow-md); }
        .page-title { font-size: 1.75rem; font-weight: 700; color: rgb(23 23 23); letter-spacing: -0.025em; }
        .dark .page-title { color: rgb(250 250 250); }
        .card { border-radius: var(--radius-2xl); border: 1px solid rgb(229 229 229 / 0.8); background: rgb(255 255 255); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: box-shadow 0.2s ease; }
        .dark .card { border-color: rgb(64 64 64 / 0.8); background: rgb(23 23 23 / 0.6); }
        .card:hover { box-shadow: var(--shadow-md); }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.25rem; border-radius: var(--radius-xl); background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%); color: white; font-weight: 600; font-size: 0.9375rem; transition: filter 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05); }
        .btn-primary:hover { filter: brightness(1.05); box-shadow: 0 4px 12px rgb(245 158 11 / 0.35); }
        .form-input { width: 100%; border-radius: var(--radius-xl); border: 1px solid rgb(212 212 212); background: rgb(255 255 255); padding: 0.625rem 1rem; font-size: 1rem; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .dark .form-input { border-color: rgb(82 82 82); background: rgb(38 38 38 / 0.8); }
        .form-input:focus { outline: none; border-color: var(--theme-primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-primary) 25%, transparent); }
        .alert-success { padding: 1rem 1.25rem; border-radius: var(--radius-xl); background: rgb(236 253 245); color: rgb(6 95 70); border: 1px solid rgb(167 243 208 / 0.6); }
        .dark .alert-success { background: rgb(6 78 59 / 0.25); color: rgb(167 243 208); border-color: rgb(6 78 59 / 0.6); }
        .alert-warning { padding: 1rem 1.25rem; border-radius: var(--radius-xl); background: rgb(255 251 235); color: rgb(146 64 14); border: 1px solid rgb(253 230 138 / 0.6); }
        .dark .alert-warning { background: rgb(120 53 15 / 0.2); color: rgb(253 230 138); border-color: rgb(180 83 9 / 0.5); }
        .prose.article-body h1 { font-size: 1.875rem; font-weight: 700; margin-bottom: 0.75rem; }
        .prose.article-body p { margin-bottom: 1rem; line-height: 1.7; color: rgb(64 64 64); }
        .dark .prose.article-body p { color: rgb(212 212 212); }
    </style>
    <script>
        (function(){
            var defaultMode = @json(app(\App\Support\ThemeManager::class)->getDefaultThemeMode());
            var cookie = document.cookie.split('; ').find(function(r){ return r.indexOf('zerrocms_theme_mode=') === 0; });
            var mode = cookie ? cookie.split('=')[1] : defaultMode;
            if (mode === 'dark') document.documentElement.classList.add('dark');
            else if (mode === 'light') document.documentElement.classList.remove('dark');
            else if (mode === 'system') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
                    document.documentElement.classList.add('dark');
                else
                    document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100 min-h-screen antialiased">
    {!! app(\App\Support\PluginManager::class)->getBodyStartHtml() !!}
    {{-- Mobile header --}}
    <header class="md:hidden bg-white/90 dark:bg-neutral-900/95 backdrop-blur-sm border-b border-neutral-200/80 dark:border-neutral-700/80 sticky top-0 z-30 rounded-b-2xl shadow-md mx-2 mt-0">
        <div class="flex items-center justify-between px-4 py-3">
            <button type="button" id="sidebar-toggle" class="p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors" aria-expanded="false" aria-controls="site-sidebar" aria-label="{{ __('nav.menu_toggle') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-semibold text-lg truncate">{{ setting('site_name', config('clan.name')) }}</span>
            <button type="button" id="theme-mode-toggle" class="p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors" aria-label="{{ __('theme.dark_mode_toggle') ?? 'Dark/Light umschalten' }}">
                <span class="theme-icon-sun hidden dark:block"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                <span class="theme-icon-moon block dark:hidden"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></span>
            </button>
        </div>
    </header>

    <div class="flex min-h-screen {{ $navRight ? 'flex-row-reverse' : '' }}">
        {{-- Sidebar --}}
        <aside id="site-sidebar" data-sidebar-close-class="{{ $navRight ? 'translate-x-full' : '-translate-x-full' }}" class="fixed md:sticky top-0 z-40 h-screen w-72 shrink-0 flex flex-col bg-white dark:bg-neutral-900 border border-neutral-200/80 dark:border-neutral-700/80 transition-all duration-300 ease-out md:my-4 {{ $navRight ? 'md:mr-4 right-0 md:translate-x-0 translate-x-full' : 'md:ml-4 left-0 -translate-x-full md:translate-x-0' }} md:rounded-2xl md:shadow-xl md:h-[calc(100vh-2rem)]">
            <div class="p-5 border-b border-neutral-200/80 dark:border-neutral-700/80 flex items-center justify-between gap-2">
                <a href="{{ url('/') }}" class="font-semibold text-lg flex items-center gap-2 min-w-0 text-neutral-800 dark:text-neutral-100">
                    @if($siteLogoUrl = \App\Support\SiteMedia::logoUrl())
                        <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', config('clan.name')) }}" class="h-10 w-auto object-contain">
                    @else
                        {{ setting('site_name', config('clan.name')) }}
                    @endif
                </a>
                <button type="button" id="theme-mode-toggle-sidebar" class="p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors shrink-0" aria-label="{{ __('theme.dark_mode_toggle') ?? 'Dark/Light umschalten' }}">
                    <span class="theme-icon-sun hidden dark:block"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                    <span class="theme-icon-moon block dark:hidden"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></span>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-2">
                @php
                    $leftMenuItems = \App\Models\MenuItem::position('left')->visible()->count();
                @endphp
                @if($leftMenuItems > 0)
                    @include('theme::partials.menu', ['position' => 'left'])
                    <ul class="flex flex-col gap-1 px-2 mt-4 pt-4 border-t border-neutral-200/80 dark:border-neutral-600/80">
                        @include('theme::partials.auth-links')
                    </ul>
                @else
                    @include('theme::partials.nav')
                @endif
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 opacity-0 invisible md:opacity-0 md:invisible transition-opacity duration-300" aria-hidden="true"></div>

        {{-- Content-Bereich: Banner + Main + Footer --}}
        <div class="flex-1 flex flex-col min-w-0">
            @if($siteBannerUrl = \App\Support\SiteMedia::bannerUrl())
                <div class="w-full">
                    @php $bannerLink = \App\Support\SiteMedia::bannerLink(); $bannerAlt = \App\Support\SiteMedia::bannerAlt(); $bannerHeightClass = \App\Support\SiteMedia::bannerHeightClass(); @endphp
                    @if($bannerLink)
                        <a href="{{ $bannerLink }}" class="block w-full {{ $bannerHeightClass }} overflow-hidden" @if(str_starts_with($bannerLink, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                            <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full h-full object-cover" role="presentation">
                        </a>
                    @else
                        <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full {{ $bannerHeightClass }} object-cover" role="presentation">
                    @endif
                </div>
            @endif
            <main class="flex-1 container mx-auto px-4 md:px-6 py-8 max-w-7xl flex gap-8 {{ $mainOrderWidgetsFirst ? 'flex-row-reverse' : '' }}">
                <div class="flex-1 min-w-0">
                    @yield('content')
                </div>
                <aside class="w-80 shrink-0 hidden lg:block space-y-5">
                    @php
                        $rightMenuItems = \App\Models\MenuItem::position('right')->visible()->ordered()->get();
                    @endphp
                    @if($rightMenuItems->isNotEmpty())
                        <div class="rounded-2xl border border-neutral-200/80 dark:border-neutral-700/80 bg-white dark:bg-neutral-900/80 p-4 shadow-sm">
                            <ul class="space-y-1">
                                @foreach($rightMenuItems as $item)
                                    <li><a href="{{ $item->resolved_url }}" class="block py-1.5 px-2 rounded theme-link-primary hover:underline text-sm" @if(str_starts_with($item->link, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {!! app(\App\Support\WidgetRenderer::class)->slot('sidebar') !!}
                </aside>
            </main>
            <footer class="border-t border-neutral-200/80 dark:border-neutral-800 bg-white/80 dark:bg-neutral-900/90 backdrop-blur-sm mt-auto py-6 rounded-t-2xl shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.06)] dark:shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.3)]">
                <div class="container mx-auto px-4 max-w-7xl text-sm text-neutral-500 dark:text-neutral-400">
                    <div class="text-center">
                        {{ setting('site_name', config('clan.name')) }} &copy; {{ date('Y') }}. {{ __('footer.copyright') }} · {{ __('footer.developed_by') }}
                        @foreach(config('clan.footer_pages', []) as $slug => $labelKey)
                            <span class="mx-2">·</span>
                            <a href="{{ route('page.show', ['slug' => $slug]) }}" class="theme-link-primary hover:underline">{{ is_string($labelKey) && str_contains($labelKey, '.') ? __($labelKey) : $labelKey }}</a>
                        @endforeach
                    </div>
                    @php
                        $contactAddress = setting('contact_address', '');
                        $contactEmail = setting('contact_email', '');
                        $contactPhone = setting('contact_phone', '');
                        $socialDiscord = setting('social_discord', '');
                        $socialFacebook = setting('social_facebook', '');
                        $socialTwitter = setting('social_twitter', '');
                        $socialYoutube = setting('social_youtube', '');
                    @endphp
                    @if($contactAddress || $contactEmail || $contactPhone || $socialDiscord || $socialFacebook || $socialTwitter || $socialYoutube)
                        <div class="mt-4 pt-4 border-t border-neutral-200/80 dark:border-neutral-700/80 flex flex-wrap justify-center gap-x-5 gap-y-1">
                            @if($contactAddress)<span>{{ $contactAddress }}</span>@endif
                            @if($contactEmail)<a href="mailto:{{ e($contactEmail) }}" class="theme-link-primary hover:underline">{{ $contactEmail }}</a>@endif
                            @if($contactPhone)<span>{{ $contactPhone }}</span>@endif
                            @if($socialDiscord)<a href="{{ e($socialDiscord) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Discord</a>@endif
                            @if($socialFacebook)<a href="{{ e($socialFacebook) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Facebook</a>@endif
                            @if($socialTwitter)<a href="{{ e($socialTwitter) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">Twitter</a>@endif
                            @if($socialYoutube)<a href="{{ e($socialYoutube) }}" target="_blank" rel="noopener noreferrer" class="theme-link-primary hover:underline">YouTube</a>@endif
                        </div>
                    @endif
                </div>
            </footer>
        </div>
    </div>

    @unless(cookie('zerrocms_cookie_consent'))
        <div id="cookie-consent" class="fixed bottom-0 left-0 right-0 bg-neutral-900/95 dark:bg-neutral-950/95 backdrop-blur-md text-neutral-200 px-6 py-4 text-center text-sm z-50 flex flex-wrap items-center justify-center gap-3 rounded-t-2xl shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.25)]">
            <span>{{ __('cookies.banner_text') }}</span>
            <a href="{{ route('page.show', ['slug' => 'cookies']) }}" class="theme-link-primary underline">{{ __('nav.cookies') }}</a>
            <button type="button" onclick="document.cookie='zerrocms_cookie_consent=1;path=/;max-age=31536000'; document.getElementById('cookie-consent').remove();" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl text-sm transition-colors">{{ __('cookies.accept') }}</button>
        </div>
    @endunless

    <script>
        (function() {
            var toggle = document.getElementById('sidebar-toggle');
            var sidebar = document.getElementById('site-sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (!toggle || !sidebar) return;
            var closeClass = sidebar.getAttribute('data-sidebar-close-class') || '-translate-x-full';
            function open() {
                sidebar.classList.remove(closeClass);
                overlay.classList.remove('opacity-0', 'invisible');
                if (overlay) overlay.classList.add('md:opacity-0', 'md:invisible');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            function close() {
                sidebar.classList.add(closeClass);
                overlay.classList.add('opacity-0', 'invisible');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
            toggle.addEventListener('click', function() {
                if (sidebar.classList.contains(closeClass)) open(); else close();
            });
            overlay.addEventListener('click', close);
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) close();
            });
        })();
        (function() {
            function setThemeMode(mode) {
                document.cookie = 'zerrocms_theme_mode=' + mode + ';path=/;max-age=31536000;SameSite=Lax';
                if (mode === 'dark') document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            }
            function toggleThemeMode() {
                var isDark = document.documentElement.classList.contains('dark');
                setThemeMode(isDark ? 'light' : 'dark');
            }
            ['theme-mode-toggle', 'theme-mode-toggle-sidebar'].forEach(function(id) {
                var btn = document.getElementById(id);
                if (btn) btn.addEventListener('click', toggleThemeMode);
            });
        })();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-meta')
    @php
        $siteName = site_name();
        $themeManager = app(\App\Support\ThemeManager::class);
        $themeColors = $themeManager->getThemeColors();
        $themeFonts = $themeManager->getThemeFonts();
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if(! empty($themeFonts['url']))
        <link href="{{ $themeFonts['url'] }}" rel="stylesheet">
    @endif
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">
    @include('theme::partials.theme-mode-boot')
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#c4a35a' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#2f6b4f' }};
            --theme-bg: {{ $themeColors['background'] ?? '#0a0c10' }};
            --theme-surface: {{ $themeColors['surface'] ?? '#141820' }};
            --theme-text: {{ $themeColors['text'] ?? '#f0e6d2' }};
            --theme-muted: {{ $themeColors['text_muted'] ?? '#a89b86' }};
            --theme-font-display: '{{ $themeFonts['display'] ?? 'UnifrakturCook' }}', 'Times New Roman', serif;
            --theme-font-body: '{{ $themeFonts['body'] ?? 'Cinzel' }}', Georgia, serif;
            --mm-brass: color-mix(in srgb, var(--theme-primary) 72%, #6b4e1e);
            --mm-stone: #1a1f28;
            --mm-parchment: #e8d9b8;
            --mm-parchment-ink: #2a2218;
            --mm-pillar-w: clamp(2.75rem, 4.2vw, 4.5rem);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/theme-medieval-modern.css') }}?v=22">
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-medieval-modern antialiased">
{!! app(\App\Support\PluginManager::class)->getBodyStartHtml() !!}
<div class="mm-page min-h-screen flex flex-col">
    @include('theme::partials.top-nav')

    {{-- Säulen nur bis zum Footer (Footer liegt bewusst darunter) --}}
    <div class="mm-hall flex-1">
        <div class="mm-hall__rails" aria-hidden="true">
            <div class="mm-hall__pillar mm-hall__pillar--left">
                <img class="mm-hall__ornament" src="{{ asset('themes/medieval-modern/side-ornament.png') }}" alt="">
            </div>
            <div class="mm-hall__pillar mm-hall__pillar--right">
                <img class="mm-hall__ornament" src="{{ asset('themes/medieval-modern/side-ornament.png') }}" alt="">
            </div>
        </div>
        <div class="mm-hall__stage">
            <div class="mm-page__body">
                @if(session('install_complete'))
                    <div class="mm-flash mb-4 mx-4 md:mx-6 panel-box rounded p-4 text-sm">
                        <p class="font-semibold mb-1" style="color: var(--theme-primary)">{{ __('install.complete_title') }}</p>
                        <p class="mb-2" style="color: var(--theme-muted)">{{ __('install.complete_body') }}</p>
                        <a href="{{ url('/admin') }}" target="_blank" rel="noopener noreferrer" class="theme-bg-primary inline-block px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider">{{ __('install.complete_admin') }}</a>
                    </div>
                @endif
                @include('theme::partials.site-shell')
            </div>
        </div>
    </div>

    <footer class="mm-footer">
        <div class="mm-rail mm-rail--footer" aria-hidden="true">
            <span class="mm-rail__line"></span>
            <span class="mm-rail__knot"></span>
            <span class="mm-rail__gem"></span>
            <span class="mm-rail__knot mm-rail__knot--flip"></span>
            <span class="mm-rail__line"></span>
        </div>
        <div class="mm-footer__inner">
            @include('theme::partials.site-footer', ['footerVariant' => 'game'])
        </div>
        <div class="mm-footer__silhouette" aria-hidden="true"></div>
    </footer>
</div>
@include('theme::partials.music-dock')
@include('theme::partials.external-links')

@include('theme::partials.flash-toasts')
</body>
</html>

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
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/theme-shell-lite.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/theme-minecraft.css') }}?v=1">
    @include('theme::partials.theme-mode-boot')
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#55c233' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#f1c232' }};
            --theme-bg: {{ $themeColors['background'] ?? '#0c100e' }};
            --theme-surface: {{ $themeColors['surface'] ?? '#161c18' }};
            --theme-text: {{ $themeColors['text'] ?? '#e8f5e9' }};
            --theme-muted: {{ $themeColors['text_muted'] ?? '#9cba9f' }};
        }
    </style>
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-minecraft theme-shell-lite antialiased">
{!! app(\App\Support\PluginManager::class)->getBodyStartHtml() !!}
<div class="gs-page">
    @include('theme::partials.top-nav')

    <div class="gs-body">
        @if(session('install_complete'))
            <div class="mb-4 gs-panel p-4 text-sm">
                <p class="font-semibold mb-1" style="color: var(--theme-primary)">{{ __('install.complete_title') }}</p>
                <p class="mb-2" style="color: var(--theme-muted)">{{ __('install.complete_body') }}</p>
                <a href="{{ url('/admin') }}" target="_blank" rel="noopener noreferrer" class="theme-bg-primary inline-block px-3 py-1.5 text-xs font-bold uppercase tracking-wider">{{ __('install.complete_admin') }}</a>
            </div>
        @endif
        @include('theme::partials.gs-site-shell')
    </div>

    <footer class="gs-footer">
        @include('theme::partials.site-footer', ['footerVariant' => 'game'])
    </footer>
</div>
@include('theme::partials.music-dock')
    @include('theme::partials.external-links')

@include('theme::partials.flash-toasts')
</body>
</html>

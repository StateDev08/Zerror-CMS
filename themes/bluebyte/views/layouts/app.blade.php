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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/theme-shell-lite.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('css/theme-bluebyte.css') }}?v=1">
    @include('theme::partials.theme-mode-boot')
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#3dd5ff' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#6b8cff' }};
            --theme-bg: {{ $themeColors['background'] ?? '#050a12' }};
            --theme-surface: {{ $themeColors['surface'] ?? '#0c1524' }};
            --theme-text: {{ $themeColors['text'] ?? '#eaf4ff' }};
            --theme-muted: {{ $themeColors['text_muted'] ?? '#7f9cb8' }};
        }
    </style>
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-bluebyte theme-shell-lite antialiased">
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
        @include('theme::partials.site-footer', ['footerVariant' => 'bluebyte'])
    </footer>
</div>

    @include('theme::partials.external-links')

@include('theme::partials.flash-toasts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName = site_name();
        $seoTitle = setting('seo_default_title', $siteName);
        $seoDescription = setting('seo_default_description', '');
        $themeColors = app(\App\Support\ThemeManager::class)->getThemeColors();
    @endphp
    <title>@yield('title', $seoTitle)</title>
    @if($seoDescription)<meta name="description" content="{{ e($seoDescription) }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @include('theme::partials.theme-mode-boot')
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#e8b86d' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#3d7ea6' }};
            --theme-bg: {{ $themeColors['background'] ?? '#0c1014' }};
            --theme-surface: {{ $themeColors['surface'] ?? '#151b22' }};
            --theme-text: {{ $themeColors['text'] ?? '#eef2f6' }};
            --theme-muted: {{ $themeColors['text_muted'] ?? '#93a4b5' }};
        }
        body.theme-example {
            margin: 0;
            min-height: 100vh;
            background: var(--theme-bg);
            color: var(--theme-text);
            font-family: 'Source Sans 3', system-ui, sans-serif;
        }
        .theme-example .font-display { font-family: 'Outfit', sans-serif; }
        .theme-example .clan-frame {
            border: 1px solid color-mix(in srgb, var(--theme-primary) 50%, transparent);
            box-shadow: 0 0 40px color-mix(in srgb, var(--theme-primary) 10%, transparent);
            position: relative;
            border-radius: 0.75rem;
        }
        .theme-example .top-nav a {
            letter-spacing: 0.08em;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--theme-primary) 90%, #fff);
            transition: color .2s;
        }
        .theme-example .top-nav a:hover { color: #fff; }
        .theme-example .hero-stage {
            background: radial-gradient(ellipse at 50% 20%, color-mix(in srgb, var(--theme-accent) 35%, #000) 0%, var(--theme-bg) 70%);
            min-height: min(56vh, 560px);
        }
        .theme-example .emblem-plate {
            background: linear-gradient(180deg, color-mix(in srgb, var(--theme-surface) 90%, #000) 0%, color-mix(in srgb, var(--theme-bg) 85%, #000) 100%);
            border: 1px solid color-mix(in srgb, var(--theme-primary) 55%, transparent);
            box-shadow: 0 20px 50px rgba(0,0,0,.45);
        }
        .theme-example .theme-link-primary { color: var(--theme-primary); }
        .theme-example .theme-bg-primary { background: var(--theme-primary); color: #111; }
        .theme-example .panel-box {
            background: color-mix(in srgb, var(--theme-surface) 96%, #000);
            border: 1px solid color-mix(in srgb, var(--theme-primary) 28%, transparent);
        }
    </style>
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-example antialiased">
{!! app(\App\Support\PluginManager::class)->getBodyStartHtml() !!}
<div class="min-h-screen flex flex-col">
    @include('theme::partials.top-nav')

    <div class="flex-1 w-full px-[1cm] py-3">
        @include('theme::partials.site-shell')
    </div>

    <footer class="mt-auto border-t border-white/10 py-6 text-center text-sm" style="color: var(--theme-muted)">
        {{ site_name() }} &copy; {{ date('Y') }}
        @foreach(config('clan.footer_pages', []) as $slug => $labelKey)
            <span class="mx-2">·</span>
            <a href="{{ route('page.show', ['slug' => $slug]) }}" class="theme-link-primary hover:underline">{{ is_string($labelKey) && str_contains($labelKey, '.') ? __($labelKey) : $labelKey }}</a>
        @endforeach
    </footer>
</div>
@include('theme::partials.external-links')
</body>
</html>

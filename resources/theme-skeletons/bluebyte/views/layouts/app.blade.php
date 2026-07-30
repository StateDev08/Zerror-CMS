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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @include('theme::partials.theme-mode-boot')
    <style>
        :root {
            --theme-primary: {{ $themeColors['primary'] ?? '#3dd5ff' }};
            --theme-accent: {{ $themeColors['accent'] ?? '#6b8cff' }};
            --theme-bg: {{ $themeColors['background'] ?? '#050a12' }};
            --theme-surface: {{ $themeColors['surface'] ?? '#0c1524' }};
            --theme-text: {{ $themeColors['text'] ?? '#eaf4ff' }};
            --theme-muted: {{ $themeColors['text_muted'] ?? '#7f9cb8' }};
            --bb-glow: color-mix(in srgb, var(--theme-primary) 45%, transparent);
            --bb-glass: color-mix(in srgb, var(--theme-surface) 78%, transparent);
        }
        body.theme-bluebyte {
            margin: 0;
            min-height: 100vh;
            color: var(--theme-text);
            font-family: 'Sora', system-ui, sans-serif;
            background:
                radial-gradient(ellipse 90% 55% at 50% -10%, color-mix(in srgb, var(--theme-accent) 28%, transparent) 0%, transparent 55%),
                radial-gradient(ellipse 60% 40% at 85% 20%, color-mix(in srgb, var(--theme-primary) 12%, transparent) 0%, transparent 50%),
                linear-gradient(180deg, #03060d 0%, var(--theme-bg) 40%, #02050a 100%);
            background-attachment: fixed;
        }
        .theme-bluebyte .font-display {
            font-family: 'Cormorant Garamond', Georgia, serif;
            letter-spacing: 0.04em;
        }
        .theme-bluebyte .bb-nav {
            border-bottom: 1px solid color-mix(in srgb, var(--theme-primary) 22%, transparent);
            background: color-mix(in srgb, #02060e 88%, transparent);
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 40px rgba(0,0,0,.35);
        }
        .theme-bluebyte .site-brand-logo {
            height: 2.25rem !important;
            max-height: 2.5rem !important;
            width: auto !important;
            max-width: 10rem !important;
            object-fit: contain !important;
            display: block;
        }
        .theme-bluebyte .top-nav a {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--theme-primary) 78%, #fff);
            transition: color .2s, text-shadow .2s;
        }
        .theme-bluebyte .top-nav a:hover {
            color: #fff;
            text-shadow: 0 0 14px var(--bb-glow);
        }
        .theme-bluebyte .bb-hex {
            width: 2rem;
            height: 2rem;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
            background: linear-gradient(145deg, var(--theme-primary), var(--theme-accent));
            box-shadow: 0 0 18px var(--bb-glow);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 800;
            color: #041018;
        }
        .theme-bluebyte .clan-frame {
            position: relative;
            border-radius: 1rem;
            border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent);
            background: color-mix(in srgb, var(--theme-surface) 55%, transparent);
            box-shadow:
                0 0 0 1px color-mix(in srgb, var(--theme-accent) 12%, transparent),
                0 25px 80px rgba(0,0,0,.45),
                inset 0 1px 0 color-mix(in srgb, #fff 8%, transparent);
            overflow: hidden;
        }
        .theme-bluebyte .clan-frame::before,
        .theme-bluebyte .clan-frame::after {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            border-color: var(--theme-primary);
            border-style: solid;
            pointer-events: none;
            z-index: 2;
            opacity: .85;
        }
        .theme-bluebyte .clan-frame::before { top: 0; left: 0; border-width: 2px 0 0 2px; }
        .theme-bluebyte .clan-frame::after { top: 0; right: 0; border-width: 2px 2px 0 0; }
        .theme-bluebyte .clan-frame-br::before { bottom: 0; left: 0; top: auto; border-width: 0 0 2px 2px; }
        .theme-bluebyte .clan-frame-br::after { bottom: 0; right: 0; top: auto; border-width: 0 2px 2px 0; }


        .theme-bluebyte .theme-link-primary { color: var(--theme-primary); }
        .theme-bluebyte .theme-link-primary:hover { text-shadow: 0 0 10px var(--bb-glow); }
        .theme-bluebyte .theme-bg-primary {
            background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent));
            color: #041018;
            font-weight: 700;
            box-shadow: 0 0 24px var(--bb-glow);
            transition: filter .2s, box-shadow .2s;
        }
        .theme-bluebyte .theme-bg-primary:hover {
            filter: brightness(1.08);
            box-shadow: 0 0 36px var(--bb-glow);
        }
        .theme-bluebyte .panel-box {
            background: var(--bb-glass);
            border: 1px solid color-mix(in srgb, var(--theme-primary) 28%, transparent);
            border-radius: 1rem;
            box-shadow: inset 0 1px 0 color-mix(in srgb, #fff 6%, transparent);
            backdrop-filter: blur(12px);
        }

        .theme-bluebyte footer a:hover { color: var(--theme-primary); }
    </style>
    {!! app(\App\Support\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-bluebyte antialiased">
{!! app(\App\Support\PluginManager::class)->getBodyStartHtml() !!}
<div class="min-h-screen flex flex-col">
    @include('theme::partials.top-nav')

    <div class="flex-1 w-full px-[1cm] py-3">
        @if(session('install_complete'))
            <div class="mb-4 panel-box p-4 text-sm">
                <p class="font-semibold mb-1" style="color: var(--theme-primary)">{{ __('install.complete_title') }}</p>
                <p class="mb-3" style="color: var(--theme-muted)">{{ __('install.complete_body') }}</p>
                <a href="{{ url('/admin') }}" target="_blank" rel="noopener noreferrer" class="theme-bg-primary inline-block px-4 py-2 rounded-lg text-xs uppercase tracking-wider">{{ __('install.complete_admin') }}</a>
            </div>
        @endif
        @include('theme::partials.site-shell')
    </div>

    <footer class="mt-auto border-t py-7 text-center text-sm" style="border-color: color-mix(in srgb, var(--theme-primary) 18%, transparent); color: var(--theme-muted)">
        @include('theme::partials.site-footer', ['footerVariant' => 'bluebyte'])
    </footer>
</div>
@include('theme::partials.external-links')
</body>
</html>

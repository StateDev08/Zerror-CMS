<?php
/**
 * One-shot generator: five distinct game themes for ZerroCMS (clan-landing style).
 */
$base = dirname(__DIR__).DIRECTORY_SEPARATOR.'themes';

$themes = [
    'minecraft' => [
        'label' => 'Minecraft',
        'description' => 'Blockige Welten, Smaragd und Gold – epische Clan-Base.',
        'font_display' => 'Press+Start+2P',
        'font_body' => 'VT323',
        'font_css' => 'Press+Start+2P&family=VT323',
        'display_size' => 'text-2xl md:text-3xl lg:text-4xl',
        'colors' => [
            'primary' => '#55c233',
            'accent' => '#f1c232',
            'background' => '#0c100e',
            'surface' => '#161c18',
            'text' => '#e8f5e9',
            'text_muted' => '#9cba9f',
        ],
        'hero_title' => 'CRAFT',
        'hero_sub' => 'TOGETHER',
        'hero_tag' => 'MINECRAFT CLAN',
        'gradient' => 'radial-gradient(ellipse at 50% 20%, #2d6a3e 0%, #0c100e 55%, #050705 100%)',
        'pattern' => 'repeating-linear-gradient(0deg, transparent, transparent 31px, rgba(85,194,51,0.08) 31px, rgba(85,194,51,0.08) 32px), repeating-linear-gradient(90deg, transparent, transparent 31px, rgba(85,194,51,0.08) 31px, rgba(85,194,51,0.08) 32px)',
        'emblem' => '<path d="M8 8h16v16H8zm24 0h16v16H32zM8 32h16v16H8zm24 0h16v16H32zM24 24h16v16H24z"/>',
        'mottos' => [
            ['Mine Deep', 'Build High'],
            ['One Server', 'One Clan'],
            ['Craft', 'Survive'],
            ['Explore', 'Conquer'],
            ['Loyalty', 'Forever'],
        ],
    ],
    'pax-dei' => [
        'label' => 'Pax Dei',
        'description' => 'Koenigliches Mittelalter – Blau und Gold wie ein Clan-Banner.',
        'font_display' => 'Cinzel',
        'font_body' => 'Rajdhani',
        'font_css' => 'Cinzel:wght@600;700&family=Rajdhani:wght@500;600;700',
        'display_size' => 'text-4xl md:text-5xl lg:text-6xl',
        'colors' => [
            'primary' => '#d4af37',
            'accent' => '#2f5fd0',
            'background' => '#07080f',
            'surface' => '#10131c',
            'text' => '#f7f3e8',
            'text_muted' => '#a9b0c3',
        ],
        'hero_title' => 'PAX',
        'hero_sub' => 'DEI',
        'hero_tag' => 'CLAN BANNER',
        'gradient' => 'radial-gradient(ellipse at 50% 15%, #1a3a8a 0%, #0a1020 45%, #05060a 100%)',
        'pattern' => 'radial-gradient(circle at 20% 80%, rgba(212,175,55,0.12), transparent 40%), radial-gradient(circle at 80% 30%, rgba(47,95,208,0.2), transparent 35%)',
        'emblem' => '<path d="M32 2l8 10h12l-6 8 4 12-18-6-18 6 4-12-6-8h12z"/>',
        'mottos' => [
            ['One Clan', 'One Family'],
            ['Stronger', 'Together'],
            ['Honor', 'Respect'],
            ['Focus', 'Victory'],
            ['Loyalty', 'Forever'],
        ],
    ],
    'seven-days' => [
        'label' => '7 Days to Die',
        'description' => 'Apokalypse, Rost und Blut – Base halten bis Tag 7.',
        'font_display' => 'Oswald',
        'font_body' => 'Share+Tech+Mono',
        'font_css' => 'Oswald:wght@500;700&family=Share+Tech+Mono',
        'display_size' => 'text-4xl md:text-5xl lg:text-6xl',
        'colors' => [
            'primary' => '#c44b2b',
            'accent' => '#8b9a6b',
            'background' => '#0e0b0a',
            'surface' => '#1a1412',
            'text' => '#f2e8e0',
            'text_muted' => '#b5a396',
        ],
        'hero_title' => 'DAY',
        'hero_sub' => 'SEVEN',
        'hero_tag' => 'SURVIVOR CLAN',
        'gradient' => 'radial-gradient(ellipse at 50% 25%, #5a2218 0%, #1a100c 50%, #080605 100%)',
        'pattern' => 'repeating-linear-gradient(135deg, transparent, transparent 12px, rgba(196,75,43,0.06) 12px, rgba(196,75,43,0.06) 13px)',
        'emblem' => '<path d="M32 4c-8 10-20 18-20 30a20 20 0 0040 0c0-12-12-20-20-30zm0 18a6 6 0 110 12 6 6 0 010-12z"/>',
        'mottos' => [
            ['Survive', 'Together'],
            ['Build', 'Fortify'],
            ['Loot', 'Reload'],
            ['Blood Moon', 'Ready'],
            ['No One', 'Left Behind'],
        ],
    ],
    'palworld' => [
        'label' => 'Palworld',
        'description' => 'Lebendige Farben, Pals und Abenteuer – Clan mit Style.',
        'font_display' => 'Nunito',
        'font_body' => 'Nunito',
        'font_css' => 'Nunito:wght@600;800',
        'display_size' => 'text-4xl md:text-5xl lg:text-6xl',
        'colors' => [
            'primary' => '#4fc3f7',
            'accent' => '#ff8a65',
            'background' => '#0a1218',
            'surface' => '#12202a',
            'text' => '#eef9ff',
            'text_muted' => '#8eb4c9',
        ],
        'hero_title' => 'PAL',
        'hero_sub' => 'WORLD',
        'hero_tag' => 'PAL TRAINERS',
        'gradient' => 'radial-gradient(ellipse at 40% 20%, #1a5f7a 0%, #0d2a3a 40%, #081018 100%)',
        'pattern' => 'radial-gradient(circle at 70% 60%, rgba(255,138,101,0.15), transparent 35%), radial-gradient(circle at 25% 40%, rgba(79,195,247,0.2), transparent 40%)',
        'emblem' => '<circle cx="32" cy="22" r="14"/><circle cx="26" cy="20" r="2" fill="#0a1218"/><circle cx="38" cy="20" r="2" fill="#0a1218"/><path d="M24 28c4 4 12 4 16 0" fill="none" stroke="#0a1218" stroke-width="2"/>',
        'mottos' => [
            ['Catch', 'Bond'],
            ['Explore', 'Together'],
            ['Base', 'Builders'],
            ['Raid', 'Ready'],
            ['Friends', 'Forever'],
        ],
    ],
    'satisfactory' => [
        'label' => 'Satisfactory',
        'description' => 'FICSIT Orange und Stahl – Fabriken bis zum Orbit.',
        'font_display' => 'Orbitron',
        'font_body' => 'Exo+2',
        'font_css' => 'Orbitron:wght@600;700&family=Exo+2:wght@500;600;700',
        'display_size' => 'text-3xl md:text-4xl lg:text-5xl',
        'colors' => [
            'primary' => '#fa9549',
            'accent' => '#4a90d9',
            'background' => '#0b0d10',
            'surface' => '#151a20',
            'text' => '#f0f3f7',
            'text_muted' => '#9aa3ad',
        ],
        'hero_title' => 'FICSIT',
        'hero_sub' => 'INC.',
        'hero_tag' => 'FACTORY CLAN',
        'gradient' => 'radial-gradient(ellipse at 50% 10%, #3d2a1a 0%, #12161c 50%, #080a0c 100%)',
        'pattern' => 'repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(250,149,73,0.05) 40px, rgba(250,149,73,0.05) 41px), repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(74,144,217,0.05) 40px, rgba(74,144,217,0.05) 41px)',
        'emblem' => '<path d="M12 40V24l20-16 20 16v16H40V28H24v12H12zm20-28l-8 6h16l-8-6z"/>',
        'mottos' => [
            ['Build', 'Automate'],
            ['Power', 'Production'],
            ['Efficiency', 'First'],
            ['Expand', 'Optimize'],
            ['Pioneer', 'Together'],
        ],
    ],
];

function layoutBlade(array $t, string $id): string
{
    $p = $t['colors']['primary'];
    $a = $t['colors']['accent'];
    $bg = $t['colors']['background'];
    $sf = $t['colors']['surface'];
    $tx = $t['colors']['text'];
    $mu = $t['colors']['text_muted'];
    $grad = $t['gradient'];
    $pat = $t['pattern'];
    $fontCss = $t['font_css'];
    $fontDisplay = str_replace('+', ' ', $t['font_display']);
    $fontBody = str_replace('+', ' ', $t['font_body']);

    return <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        \$siteName = site_name();
        \$seoTitle = setting('seo_default_title', \$siteName);
        \$seoDescription = setting('seo_default_description', '');
        \$themeColors = app(\\App\\Support\\ThemeManager::class)->getThemeColors();
    @endphp
    <title>@yield('title', \$seoTitle)</title>
    @if(\$seoDescription)<meta name="description" content="{{ e(\$seoDescription) }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={$fontCss}&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        :root {
            --theme-primary: {{ \$themeColors['primary'] ?? '{$p}' }};
            --theme-accent: {{ \$themeColors['accent'] ?? '{$a}' }};
            --theme-bg: {{ \$themeColors['background'] ?? '{$bg}' }};
            --theme-surface: {{ \$themeColors['surface'] ?? '{$sf}' }};
            --theme-text: {{ \$themeColors['text'] ?? '{$tx}' }};
            --theme-muted: {{ \$themeColors['text_muted'] ?? '{$mu}' }};
        }
        body.theme-{$id} {
            margin: 0;
            min-height: 100vh;
            background: var(--theme-bg);
            color: var(--theme-text);
            font-family: '{$fontBody}', system-ui, sans-serif;
        }
        .theme-{$id} .font-display { font-family: '{$fontDisplay}', serif; }
        .theme-{$id} .clan-frame {
            border: 1px solid color-mix(in srgb, var(--theme-primary) 55%, transparent);
            box-shadow:
                inset 0 0 0 1px color-mix(in srgb, var(--theme-primary) 25%, transparent),
                0 0 40px color-mix(in srgb, var(--theme-primary) 12%, transparent);
            position: relative;
        }
        .theme-{$id} .clan-frame::before,
        .theme-{$id} .clan-frame::after {
            content: '';
            position: absolute;
            width: 18px; height: 18px;
            border-color: var(--theme-primary);
            border-style: solid;
            pointer-events: none;
        }
        .theme-{$id} .clan-frame::before { top: -1px; left: -1px; border-width: 2px 0 0 2px; }
        .theme-{$id} .clan-frame::after { top: -1px; right: -1px; border-width: 2px 2px 0 0; }
        .theme-{$id} .clan-frame-br::before { bottom: -1px; left: -1px; top: auto; border-width: 0 0 2px 2px; }
        .theme-{$id} .clan-frame-br::after { bottom: -1px; right: -1px; top: auto; border-width: 0 2px 2px 0; }
        .theme-{$id} .top-nav a {
            letter-spacing: 0.12em;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--theme-primary) 85%, #fff);
            transition: color .2s, text-shadow .2s;
        }
        .theme-{$id} .top-nav a:hover {
            color: #fff;
            text-shadow: 0 0 12px color-mix(in srgb, var(--theme-primary) 60%, transparent);
        }
        .theme-{$id} .theme-link-primary { color: var(--theme-primary); }
        .theme-{$id} .theme-bg-primary { background: var(--theme-primary); color: #111; }
        .theme-{$id} .panel-box {
            background: color-mix(in srgb, var(--theme-surface) 95%, #000);
            border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent);
        }
        .theme-{$id} .music-dock {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 40;
            min-width: 220px;
            background: color-mix(in srgb, var(--theme-surface) 92%, #000);
            border: 1px solid color-mix(in srgb, var(--theme-primary) 45%, transparent);
            box-shadow: 0 12px 40px rgba(0,0,0,.45);
        }
    </style>
    {!! app(\\App\\Support\\PluginManager::class)->getHeadHtml() !!}
</head>
<body class="theme-{$id} antialiased">
{!! app(\\App\\Support\\PluginManager::class)->getBodyStartHtml() !!}
<div class="min-h-screen flex flex-col">
    @include('theme::partials.top-nav')

    <div class="flex-1 w-full max-w-[1400px] mx-auto px-3 md:px-6 py-4 md:py-6">
        @if(session('install_complete'))
            <div class="mb-4 panel-box rounded p-4 text-sm">
                <p class="font-semibold mb-1" style="color: var(--theme-primary)">{{ __('install.complete_title') }}</p>
                <p class="mb-2" style="color: var(--theme-muted)">{{ __('install.complete_body') }}</p>
                @if(session('install_post_steps'))
                    <ul class="list-disc list-inside text-xs space-y-0.5 mb-3" style="color: var(--theme-muted)">
                        @foreach(session('install_post_steps') as \$stepDone)
                            <li>{{ \$stepDone }}</li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ url('/admin') }}" class="theme-bg-primary inline-block px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider">{{ __('install.complete_admin') }}</a>
            </div>
        @endif
        <div class="clan-frame clan-frame-br bg-black/40 overflow-hidden">
            {{-- Banner/Slider nur im Hero; auf Unterseiten als Strip --}}
            @hasSection('hero')
                @yield('hero')
            @else
                @include('theme::partials.site-banner')
            @endif
            <main class="px-4 md:px-8 py-6 md:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <footer class="mt-auto border-t border-white/10 py-6 text-center text-sm" style="color: var(--theme-muted)">
        @include('theme::partials.site-footer', ['footerVariant' => 'game'])
    </footer>
</div>
@include('theme::partials.music-dock')
</body>
</html>
BLADE;
}

function topNavBlade(): string
{
    return <<<'BLADE'
<header class="sticky top-0 z-50 border-b border-white/10 bg-black/85 backdrop-blur-md">
    <div class="w-full px-4 md:px-6 h-12 md:h-14 flex items-center gap-3 md:gap-5">
        <div class="flex items-center gap-3 md:gap-5 min-w-0 flex-1">
            <a href="{{ url('/') }}" class="flex items-center shrink-0" data-same-tab>
                @include('theme::partials.site-brand', ['variant' => 'game'])
            </a>
            <nav class="top-nav hidden md:flex items-center gap-4 lg:gap-6 min-w-0 overflow-x-auto">
                @include('theme::partials.top-nav-links')
            </nav>
        </div>
        @include('theme::partials.user-menu')
    </div>
</header>
BLADE;
}

function homeBlade(array $t): string
{
    return <<<'BLADE'
@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('nav.home'))

@section('hero')
<section class="relative flex flex-col">
    @include('theme::partials.hero-media')
</section>
@endsection

@section('content')
    @include('theme::partials.home-content')
@endsection
BLADE;
}

foreach ($themes as $id => $meta) {
    $dir = $base.DIRECTORY_SEPARATOR.$id;
    foreach (['views/layouts', 'views/partials'] as $sub) {
        $p = $dir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $sub);
        if (! is_dir($p)) {
            mkdir($p, 0755, true);
        }
    }

    $json = [
        'name' => $id,
        'version' => '1.0.0',
        'parent' => 'common',
        'selectable' => true,
        'label' => $meta['label'],
        'description' => $meta['description'],
        'colors' => $meta['colors'],
    ];
    file_put_contents($dir.DIRECTORY_SEPARATOR.'theme.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
    file_put_contents($dir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'app.blade.php', layoutBlade($meta, $id));
    file_put_contents($dir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'partials'.DIRECTORY_SEPARATOR.'top-nav.blade.php', topNavBlade());
    file_put_contents($dir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'home.blade.php', homeBlade($meta));
    echo "Created theme: {$id}\n";
}

echo "Done.\n";

{{-- Site-Logo oder Text-Fallback für die Kopfzeile --}}
@php
    $brand = site_name();
    $logoUrl = \App\Support\SiteMedia::logoUrl();
    $variant = $variant ?? 'game';
    $logoHeight = site_logo_height_css();
    $logoMaxWidth = site_logo_max_width_css();
@endphp
@once
<style>
    :root {
        --site-logo-height: {{ $logoHeight }};
        --site-logo-max-width: {{ $logoMaxWidth }};
        --site-logo-slot: 2.5rem;
    }
    /* Layout-Slot bleibt klein (Menühöhe); sichtbare Größe via height + Negativ-Margin */
    .site-brand-logo {
        height: var(--site-logo-height) !important;
        max-height: none !important;
        width: auto !important;
        max-width: min(100%, var(--site-logo-max-width)) !important;
        object-fit: contain !important;
        display: block;
        margin-top: calc((var(--site-logo-slot) - var(--site-logo-height)) / 2) !important;
        margin-bottom: calc((var(--site-logo-slot) - var(--site-logo-height)) / 2) !important;
        position: relative;
        z-index: 6;
    }
    a:has(> .site-brand-logo),
    .mm-brand:has(.site-brand-logo) {
        height: var(--site-logo-slot);
        max-height: var(--site-logo-slot);
        overflow: visible;
        display: flex;
        align-items: center;
        flex-shrink: 1;
        min-width: 0;
        max-width: min(46vw, 11rem);
        position: relative;
        z-index: 6;
    }
    @media (max-width: 767px) {
        a:has(> .site-brand-logo),
        .mm-brand:has(.site-brand-logo) {
            max-width: min(36vw, 7.25rem);
        }
        .site-brand-logo {
            height: auto !important;
            max-height: 2.65rem !important;
            max-width: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
    }
</style>
@endonce
@if($logoUrl)
    <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="site-brand-logo">
@elseif($variant === 'bluebyte')
    <span class="bb-hex" aria-hidden="true">BB</span>
    <span class="font-display text-base md:text-lg tracking-[0.12em] uppercase text-white group-hover:text-[var(--theme-primary)] transition-colors">
        {{ \Illuminate\Support\Str::limit($brand, 20) }}
    </span>
@else
    <span class="font-display text-sm md:text-base tracking-[0.2em] uppercase" style="color: var(--theme-primary)">
        {{ \Illuminate\Support\Str::limit($brand, 18) }}
    </span>
@endif

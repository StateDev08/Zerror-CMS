@php
    $renderer = app(\App\Support\WidgetRenderer::class);
    // Sections zuerst – Widgets leeren sonst Blade-Sections.
    $heroHtml = $__env->hasSection('hero') ? $__env->yieldContent('hero') : null;
    $mainContent = $__env->yieldContent('content');
    $leftHtml = $renderer->slot('left');
    $rightHtml = $renderer->slot('right');
    $hasLeftWidgets = trim(strip_tags($leftHtml)) !== '';
    $hasRightWidgets = trim(strip_tags($rightHtml)) !== '';
    $hasTopMedia = $heroHtml !== null && trim(strip_tags((string) $heroHtml)) !== '';
    $gridMod = match (true) {
        $hasLeftWidgets && $hasRightWidgets => 'mm-grid--both',
        $hasLeftWidgets => 'mm-grid--left',
        $hasRightWidgets => 'mm-grid--right',
        default => 'mm-grid--main',
    };
@endphp
<link rel="stylesheet" href="{{ asset('css/cms-widgets.css') }}?v=11">
<link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">

{{-- 1) Slider im Hallen-Rahmen (Säulen kommen aus dem Layout) --}}
<div class="mm-slider-band">
    <div class="mm-portal">
        <span class="mm-portal__crest" aria-hidden="true"></span>
        <div class="mm-portal__arch">
            <div class="mm-portal__viewport">
                @if($hasTopMedia)
                    {!! $heroHtml !!}
                @else
                    @include('theme::partials.site-banner')
                @endif
            </div>
        </div>
        <div class="mm-portal__sill" aria-hidden="true">
            <span class="mm-portal__gem"></span>
        </div>
    </div>
</div>

{{-- 2) Zierbalken --}}
<div class="mm-rail" aria-hidden="true">
    <span class="mm-rail__line"></span>
    <span class="mm-rail__knot"></span>
    <span class="mm-rail__gem"></span>
    <span class="mm-rail__knot mm-rail__knot--flip"></span>
    <span class="mm-rail__line"></span>
</div>

{{-- 3) Drei Spalten: Widgets | Inhalt | Widgets --}}
<div class="mm-grid {{ $gridMod }}">
    @if($hasLeftWidgets)
        <aside class="mm-col mm-col--left">
            {!! $leftHtml !!}
        </aside>
    @endif

    <div class="mm-col mm-col--main">
        <main class="mm-main-panel">
            {!! $mainContent !!}
        </main>
    </div>

    @if($hasRightWidgets)
        <aside class="mm-col mm-col--right">
            {!! $rightHtml !!}
        </aside>
    @endif
</div>

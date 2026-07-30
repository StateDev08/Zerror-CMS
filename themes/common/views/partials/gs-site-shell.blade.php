@php
    $renderer = app(\App\Support\WidgetRenderer::class);
    $heroHtml = $__env->hasSection('hero') ? $__env->yieldContent('hero') : null;
    $mainContent = $__env->yieldContent('content');
    $leftHtml = $renderer->slot('left');
    $rightHtml = $renderer->slot('right');
    $hasLeftWidgets = trim(strip_tags($leftHtml)) !== '';
    $hasRightWidgets = trim(strip_tags($rightHtml)) !== '';
    $hasTopMedia = $heroHtml !== null && trim(strip_tags((string) $heroHtml)) !== '';
    $gridMod = match (true) {
        $hasLeftWidgets && $hasRightWidgets => 'gs-grid--both',
        $hasLeftWidgets => 'gs-grid--left',
        $hasRightWidgets => 'gs-grid--right',
        default => 'gs-grid--main',
    };
@endphp
<link rel="stylesheet" href="{{ asset('css/cms-widgets.css') }}?v=11">
<link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">

<section class="gs-hero" aria-label="Hero">
    @if($hasTopMedia)
        {!! $heroHtml !!}
    @else
        @include('theme::partials.site-banner')
    @endif
</section>

<div class="gs-grid {{ $gridMod }}">
    @if($hasLeftWidgets)
        <aside class="gs-col gs-col--left">
            {!! $leftHtml !!}
        </aside>
    @endif

    <div class="gs-col gs-main">
        <main class="gs-panel">
            {!! $mainContent !!}
        </main>
    </div>

    @if($hasRightWidgets)
        <aside class="gs-col gs-col--right">
            {!! $rightHtml !!}
        </aside>
    @endif
</div>

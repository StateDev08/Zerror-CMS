@php
    $renderer = app(\App\Support\WidgetRenderer::class);
    $leftHtml = $renderer->slot('left');
    $rightHtml = $renderer->slot('right');
    $hasLeftWidgets = trim(strip_tags($leftHtml)) !== '';
    $hasRightWidgets = trim(strip_tags($rightHtml)) !== '';
    $gridMod = match (true) {
        $hasLeftWidgets && $hasRightWidgets => 'global-widgets-grid--both',
        $hasLeftWidgets => 'global-widgets-grid--left',
        $hasRightWidgets => 'global-widgets-grid--right',
        default => 'global-widgets-grid--main',
    };
@endphp
<style>
    .global-widgets-grid {
        display: grid;
        gap: 1cm;
        grid-template-columns: minmax(0, 1fr);
        align-items: start;
        width: 100%;
    }
    .global-widgets-main { min-width: 0; width: 100%; }
    .global-widgets-left,
    .global-widgets-right {
        min-width: 0;
        width: 100%;
        height: fit-content;
        align-self: start;
        display: flex;
        flex-direction: column;
        gap: 0.75cm;
        padding: 0.75cm;
        box-sizing: border-box;
    }
    @media (max-width: 1023px) {
        .global-widgets-main { order: -1; }
        .global-widgets-grid { gap: 0.75cm; }
    }
    @media (min-width: 1024px) {
        .global-widgets-grid--both {
            grid-template-columns: minmax(12rem, 16rem) minmax(0, 1fr) minmax(12rem, 16rem);
        }
        .global-widgets-grid--left {
            grid-template-columns: minmax(12rem, 16rem) minmax(0, 1fr);
        }
        .global-widgets-grid--right {
            grid-template-columns: minmax(0, 1fr) minmax(12rem, 16rem);
        }
        .global-widgets-main { order: 0; }
    }
</style>
<div class="global-widgets-grid {{ $gridMod }}">
    @if($hasLeftWidgets)
        <aside class="global-widgets-left panel-box">
            {!! $leftHtml !!}
        </aside>
    @endif
    <div class="global-widgets-main">
        {!! $__env->yieldContent('content') !!}
    </div>
    @if($hasRightWidgets)
        <aside class="global-widgets-right panel-box">
            {!! $rightHtml !!}
        </aside>
    @endif
</div>

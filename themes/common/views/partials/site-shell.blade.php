@php
    $renderer = app(\App\Support\WidgetRenderer::class);
    // Wichtig: Sections ZUERST yielden – jedes view()->render() in Widgets
    // leert die Blade-Sections und würde sonst den Hauptinhalt verschlucken.
    $heroHtml = $__env->hasSection('hero') ? $__env->yieldContent('hero') : null;
    $mainContent = $__env->yieldContent('content');
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
{{-- Drei Spalten; horizontal max. ~1cm Abstand überall --}}
<link rel="stylesheet" href="{{ asset('css/cms-widgets.css') }}?v=11">
<link rel="stylesheet" href="{{ asset('css/cms-content.css') }}?v=4">
<style>
    .global-widgets-grid {
        display: grid;
        gap: clamp(0.75rem, 2.5vw, 1cm);
        grid-template-columns: minmax(0, 1fr);
        align-items: start;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .global-widgets-main {
        min-width: 0;
        width: 100%;
        max-width: 100%;
    }
    .global-widgets-main > main {
        padding: clamp(0.75rem, 2.5vw, 1cm);
    }
    .global-widgets-left,
    .global-widgets-right {
        min-width: 0;
        width: 100%;
        max-width: 100%;
        height: fit-content;
        align-self: start;
        display: flex;
        flex-direction: column;
        gap: clamp(0.55rem, 2vw, 0.75cm);
        padding: 0;
        box-sizing: border-box;
        background: transparent;
        border: 0;
    }
    @media (max-width: 767px) {
        .global-widgets-main > main {
            padding: 0.75rem 0.65rem;
        }
        .global-widgets-grid { gap: 0.75rem; }
    }
    @media (max-width: 1023px) {
        .global-widgets-main { order: -1; }
        .global-widgets-grid { gap: 0.75rem; }
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
        .global-widgets-left {
            border-radius: 1rem 0.35rem 0.35rem 1rem;
        }
        .global-widgets-right {
            border-radius: 0.35rem 1rem 1rem 0.35rem;
        }
        .global-widgets-main.clan-frame {
            border-radius: 0.35rem;
        }
    }
</style>
<div class="global-widgets-grid {{ $gridMod }}">
    @if($hasLeftWidgets)
        <aside class="global-widgets-left">
            {!! $leftHtml !!}
        </aside>
    @endif

    <div class="global-widgets-main clan-frame clan-frame-br">
        @if($heroHtml !== null)
            {!! $heroHtml !!}
        @else
            @include('theme::partials.site-banner')
        @endif
        <main>
            {!! $mainContent !!}
        </main>
    </div>

    @if($hasRightWidgets)
        <aside class="global-widgets-right">
            {!! $rightHtml !!}
        </aside>
    @endif
</div>

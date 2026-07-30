<div class="cms-widget cms-widget--voice">
    <header class="cms-widget__head">
        <div class="cms-widget__brand">
            <span class="cms-widget__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3z"/><path d="M19 10v1a7 7 0 0 1-14 0v-1M12 18v3"/></svg>
            </span>
            <h3 class="cms-widget__title" style="border:0;padding:0;margin:0;letter-spacing:0.08em">{{ $title }}</h3>
        </div>
    </header>
    @if($hostLabel !== '')
        <p class="cms-widget__meta">{{ $hostLabel }}</p>
    @endif
    @if($hint !== '')
        <p class="cms-widget__hint" style="margin-top:0.5rem">{{ $hint }}</p>
    @endif
    @if($connectUrl !== '')
        <a class="cms-widget__cta" href="{{ $connectUrl }}">{{ $buttonText }}</a>
    @else
        <p class="cms-widget__hint">{{ __('widgets.voice_configure') }}</p>
    @endif
</div>
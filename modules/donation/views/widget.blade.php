<div class="cms-widget cms-widget--donation">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
    </header>
    @if($intro !== '')
        <p class="cms-widget__hint">{{ $intro }}</p>
    @endif
    @if($url !== '')
        <a class="cms-widget__cta" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $buttonText }}</a>
    @else
        <p class="cms-widget__hint">{{ __('widgets.donation_configure') }}</p>
    @endif
</div>

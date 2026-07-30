<div class="cms-widget cms-widget--stream">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
    </header>
    @if($mode === 'link')
        @if($watchUrl)
            <a class="cms-widget__cta" href="{{ $watchUrl }}" target="_blank" rel="noopener noreferrer">{{ $buttonText }}</a>
        @else
            <p class="cms-widget__hint">{{ __('widgets.stream_configure') }}</p>
        @endif
    @else
        @if($embedUrl)
            <div style="position:relative;padding-top:56.25%;border-radius:0.5rem;overflow:hidden;background:#000">
                <iframe src="{{ $embedUrl }}" title="{{ $title }}" allowfullscreen loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;border:0" allow="autoplay;encrypted-media;picture-in-picture"></iframe>
            </div>
            @if($chatUrl)
                <iframe src="{{ $chatUrl }}" title="Chat" loading="lazy" style="margin-top:0.5rem;width:100%;height:220px;border:0;border-radius:0.5rem;background:#000"></iframe>
            @endif
        @else
            <p class="cms-widget__hint">{{ __('widgets.stream_configure') }}</p>
        @endif
    @endif
</div>

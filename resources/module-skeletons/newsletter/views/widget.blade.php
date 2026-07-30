<div class="cms-widget cms-widget--newsletter">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
        @if($showPageLink && \Illuminate\Support\Facades\Route::has('newsletter.index'))
            <a href="{{ route('newsletter.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>
    @if($intro !== '')
        <p class="cms-widget__hint">{{ $intro }}</p>
    @endif
    @if(\Illuminate\Support\Facades\Route::has('newsletter.subscribe'))
        <form method="post" action="{{ route('newsletter.subscribe') }}" style="margin-top:0.75rem;display:flex;flex-direction:column;gap:0.45rem">
            @csrf
            <label class="sr-only" for="nl-email-{{ $uniq }}">E-Mail</label>
            <input id="nl-email-{{ $uniq }}" type="email" name="email" required placeholder="name@example.com" style="width:100%;padding:0.55rem 0.65rem;border-radius:0.45rem;border:1px solid color-mix(in srgb,var(--theme-primary) 30%,transparent);background:color-mix(in srgb,#000 25%,transparent);color:inherit">
            <button type="submit" class="cms-widget__cta" style="border:0;cursor:pointer">{{ $buttonText }}</button>
        </form>
    @endif
</div>

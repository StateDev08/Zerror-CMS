<div class="cms-widget cms-widget--social">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
    </header>
    @if(empty($links))
        <p class="cms-widget__hint">{{ __('widgets.social_empty') }}</p>
    @else
        <ul class="cms-social-list" style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:0.4rem">
            @foreach($links as $link)
                <li>
                    <a href="{{ $link['url'] }}" class="cms-feed__item" target="_blank" rel="noopener noreferrer" style="padding:0.45rem 0.55rem;border:1px solid color-mix(in srgb,var(--theme-primary) 22%,transparent);border-radius:0.45rem;display:flex;justify-content:space-between;text-decoration:none;color:inherit">
                        <span class="cms-feed__title">{{ $link['label'] }}</span>
                        <span class="cms-feed__chevron" aria-hidden="true">›</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
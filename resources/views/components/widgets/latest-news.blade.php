<div class="cms-widget cms-widget--news">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title ?? __('widgets.latest_news') }}</h3>
        @if($showAllLink ?? false)
            <a href="{{ route('news.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>

    @if($posts->isEmpty())
        <p class="cms-widget__hint">{{ $emptyText ?? __('widgets.no_news') }}</p>
    @else
        <ul class="cms-feed">
            @foreach($posts as $post)
                <li>
                    <a href="{{ route('news.show', $post->slug) }}" class="cms-feed__item">
                        <span class="cms-feed__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h12a2 2 0 0 1 2 2v12H6a2 2 0 0 1-2-2V5z"/><path d="M8 9h8M8 13h6"/></svg>
                        </span>
                        <span class="cms-feed__body">
                            <span class="cms-feed__title">{{ $post->title }}</span>
                            <span class="cms-feed__meta">{{ $post->created_at?->format(__('general.date_format')) }}</span>
                        </span>
                        <span class="cms-feed__chevron" aria-hidden="true">›</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

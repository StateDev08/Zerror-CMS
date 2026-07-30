<div class="cms-widget cms-widget--forum">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title ?? __('widgets.latest_forum_posts') }}</h3>
        @if($showAllLink ?? false)
            <a href="{{ route('forum.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>

    @if($posts->isEmpty())
        <p class="cms-widget__hint">{{ $emptyText ?? __('widgets.no_forum_posts') }}</p>
    @else
        <ul class="cms-feed">
            @foreach($posts as $post)
                @php
                    $plain = trim(html_entity_decode(strip_tags((string) $post->body), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $threadTitle = $post->thread?->title ?: __('widgets.latest_forum_posts');
                    $href = $post->thread ? route('forum.thread.show', $post->thread) : '#';
                    $len = (int) ($excerptLength ?? 48);
                @endphp
                <li>
                    <a href="{{ $href }}" class="cms-feed__item">
                        <span class="cms-feed__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </span>
                        <span class="cms-feed__body">
                            <span class="cms-feed__title">{{ $threadTitle }}</span>
                            <span class="cms-feed__meta">
                                {{ $post->user?->name ?? __('forum.guest') }}
                                @if($plain !== '')
                                    · {{ \Illuminate\Support\Str::limit($plain, $len) }}
                                @endif
                                · {{ $post->created_at?->format(__('general.date_format')) }}
                            </span>
                        </span>
                        <span class="cms-feed__chevron" aria-hidden="true">›</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<div class="cms-widget cms-widget--events">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title ?? __('widgets.upcoming_events') }}</h3>
        @if($showAllLink ?? false)
            <a href="{{ route('calendar.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>

    @if($events->isEmpty())
        <p class="cms-widget__hint">{{ $emptyText ?? __('widgets.no_events') }}</p>
    @else
        <ul class="cms-event-list">
            @foreach($events as $event)
                <li>
                    <a href="{{ route('calendar.show', $event->id) }}" class="cms-event-list__item">
                        <time class="cms-event-list__date" datetime="{{ $event->starts_at?->toIso8601String() }}">
                            <span class="cms-event-list__day">{{ $event->starts_at?->format('d') }}</span>
                            <span class="cms-event-list__month">{{ $event->starts_at?->translatedFormat('M') }}</span>
                        </time>
                        <span class="cms-event-list__body">
                            <span class="cms-event-list__title">{{ $event->title }}</span>
                            <span class="cms-event-list__meta">
                                {{ $event->starts_at?->format(site_datetime_format()) }}
                                @if(($showLocation ?? true) && $event->location)
                                    · {{ $event->location }}
                                @endif
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

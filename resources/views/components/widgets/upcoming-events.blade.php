<div class="widget widget-upcoming-events rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('widgets.upcoming_events') }}</h3>
    @if($events->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('widgets.no_events') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($events as $event)
                <li>
                    <a href="{{ route('calendar.show', $event->id) }}" class="text-sm text-gray-700 dark:text-gray-300 hover:underline">
                        {{ $event->title }}
                    </a>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $event->starts_at->format(__('general.date_format')) }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>

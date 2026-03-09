<div class="widget widget-latest-news rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('widgets.latest_news') }}</h3>
    @if($posts->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('widgets.no_news') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($posts as $post)
                <li>
                    <a href="{{ route('news.show', $post->slug) }}" class="text-sm text-gray-700 dark:text-gray-300 hover:underline">
                        {{ $post->title }}
                    </a>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->format(__('general.date_format')) }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>

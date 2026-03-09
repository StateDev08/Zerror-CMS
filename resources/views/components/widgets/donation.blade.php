<div class="widget widget-donation rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('widgets.donation_title') }}</h3>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('widgets.donation_intro') }}</p>
    <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium">
        {{ $buttonText }}
    </a>
</div>

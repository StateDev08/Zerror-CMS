<div class="widget widget-cookie-banner fixed bottom-0 left-0 right-0 z-50 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-lg" x-data="{ show: true }" x-show="show" x-cloak>
    <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $banner_text ?? 'Wir verwenden Cookies.' }}</p>
        <div class="flex items-center gap-2">
            @if(!empty($privacy_url))
                <a href="{{ $privacy_url }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">{{ __('nav.privacy', [], 'Datenschutz') }}</a>
            @endif
            <button type="button" x-on:click="show = false" class="px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium">{{ $button_text ?? 'Verstanden' }}</button>
        </div>
    </div>
</div>

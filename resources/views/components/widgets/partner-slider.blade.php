@php
    $visibleCount = max(1, (int) $visibleCount);
@endphp
<div class="widget widget-partner-slider rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4" data-speed="{{ (int) $speed }}" data-visible="{{ $visibleCount }}">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ __('widgets.partner_slider_title', [], 'Partner') }}</h3>
    @if($partners->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('partners.none', [], 'Keine Partner.') }}</p>
    @else
        <div class="flex flex-nowrap gap-4 overflow-x-auto pb-2" style="scroll-behavior: smooth;">
            @foreach($partners as $partner)
                <a href="{{ $partner->url }}" target="_blank" rel="noopener" class="flex-shrink-0 flex items-center gap-2 rounded border border-gray-200 dark:border-gray-600 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 min-w-[120px] max-w-[180px]" title="{{ $partner->name }}">
                    @if($partner->logo)
                        <img src="{{ storage_asset($partner->logo) }}" alt="{{ $partner->name }}" class="h-10 w-auto object-contain">
                    @endif
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $partner->name }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Menüeinträge aus der Datenbank für eine Position (top|left|right) --}}
@php
    $position = $position ?? 'left';
    $linkClass = $navLinkClass ?? 'text-neutral-700 dark:text-neutral-300 hover:text-[var(--theme-primary)] dark:hover:text-[var(--theme-primary)]';
    $items = \App\Models\MenuItem::position($position)->visible()->ordered()->get();
@endphp
@if($items->isNotEmpty())
<ul class="flex flex-col gap-1">
    @foreach($items as $item)
        <li>
            <a href="{{ $item->resolved_url }}" class="block py-2.5 px-3 rounded-xl font-medium transition-colors {{ $linkClass }}" data-same-tab>{{ $item->label }}</a>
        </li>
    @endforeach
</ul>
@endif

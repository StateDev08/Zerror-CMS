{{-- Menüeinträge aus der Datenbank für eine Position (left|right) --}}
@php
    $position = $position ?? 'left';
    $linkClass = $navLinkClass ?? 'text-neutral-700 dark:text-neutral-300 hover:text-[var(--theme-primary)] dark:hover:text-[var(--theme-primary)]';
    $items = \App\Models\MenuItem::position($position)->visible()->ordered()->get();
@endphp
@if($items->isNotEmpty())
<ul class="flex flex-col gap-1">
    @foreach($items as $item)
        <li>
            <a href="{{ $item->resolved_url }}" class="block py-2.5 px-3 rounded-xl font-medium transition-colors {{ $linkClass }}" @if(str_starts_with($item->link, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
        </li>
    @endforeach
</ul>
@endif

<div class="cms-widget cms-widget--partners" data-partner-speed="{{ (int) $speedMs }}">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
        @if(\Illuminate\Support\Facades\Route::has('partners.index'))
            <a href="{{ route('partners.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>
    @if($partners->isEmpty())
        <p class="cms-widget__hint">{{ __('widgets.partners_empty') }}</p>
    @else
        <div class="cms-partners" style="display:grid;grid-template-columns:repeat({{ max(1, min(6, (int)$visibleCount)) }},minmax(0,1fr));gap:0.5rem;align-items:center">
            @foreach($partners->take(max(1, (int)$visibleCount * 2)) as $partner)
                @php
                    $href = trim((string) $partner->url);
                    $logo = trim((string) $partner->logo);
                    $logoUrl = $logo !== '' ? (str_starts_with($logo, 'http') ? $logo : storage_asset($logo)) : null;
                @endphp
                <div style="text-align:center">
                    @if($href !== '')
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" loading="lazy" style="max-height:2.5rem;max-width:100%;object-fit:contain">
                            @else
                                <span style="font-size:0.8rem">{{ $partner->name }}</span>
                            @endif
                        </a>
                    @else
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" loading="lazy" style="max-height:2.5rem;max-width:100%;object-fit:contain">
                        @else
                            <span style="font-size:0.8rem">{{ $partner->name }}</span>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

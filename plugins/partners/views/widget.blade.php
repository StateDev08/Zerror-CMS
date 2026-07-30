<div class="cms-widget cms-widget--partners" data-partner-speed="{{ (int) $speedMs }}">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title ?: __('nav.partners') }}</h3>
        @if(\Illuminate\Support\Facades\Route::has('partners.index'))
            <a href="{{ route('partners.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>
    @if($partners->isEmpty())
        <p class="cms-widget__hint">{{ __('widgets.partners_empty') }}</p>
    @else
        <ul class="cms-partners-list">
            @foreach($partners->take(max(1, (int) $visibleCount)) as $partner)
                @php
                    $href = trim((string) $partner->url);
                    $logo = trim((string) $partner->logo);
                    $logoUrl = $logo !== '' ? (str_starts_with($logo, 'http') ? $logo : storage_asset($logo)) : null;
                    $name = trim((string) $partner->name);
                @endphp
                <li class="cms-partner-card">
                    @if($href !== '')
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="cms-partner-card__link">
                            @if($logoUrl)
                                <span class="cms-partner-card__logo">
                                    <img src="{{ $logoUrl }}" alt="" loading="lazy">
                                </span>
                            @endif
                            <span class="cms-partner-card__name">{{ $name !== '' ? $name : __('nav.partners') }}</span>
                        </a>
                    @else
                        <div class="cms-partner-card__link">
                            @if($logoUrl)
                                <span class="cms-partner-card__logo">
                                    <img src="{{ $logoUrl }}" alt="" loading="lazy">
                                </span>
                            @endif
                            <span class="cms-partner-card__name">{{ $name !== '' ? $name : __('nav.partners') }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>

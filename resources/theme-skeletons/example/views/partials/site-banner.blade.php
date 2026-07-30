{{-- Site-Banner (ACP: Einstellungen → Banner) – Höhe folgt dem Bild --}}
@if($siteBannerUrl = \App\Support\SiteMedia::bannerUrl())
    @php
        $bannerLink = \App\Support\SiteMedia::bannerLink();
        $bannerAlt = \App\Support\SiteMedia::bannerAlt();
    @endphp
    <div class="site-banner w-full overflow-hidden border-b" style="border-color: color-mix(in srgb, var(--theme-primary, #c9a227) 35%, transparent)">
        @if($bannerLink)
            <a href="{{ $bannerLink }}" class="block w-full"{!! link_new_tab_attrs($bannerLink) !!}>
                <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full h-auto block max-w-full" role="presentation">
            </a>
        @else
            <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full h-auto block max-w-full" role="presentation">
        @endif
    </div>
@endif

{{-- Hero-Medienfläche: Slider (Priorität) oder Site-Banner. Höhe folgt dem Bild. --}}
@php
    $sliderSlides = \App\Models\SliderSlide::query()->where('active', true)->orderBy('order')->get();
    $slideDuration = max(2, min(30, (int) setting('slider_duration_seconds', 5))) * 1000;
    $showArrows = (bool) filter_var(setting('slider_show_arrows', '1'), FILTER_VALIDATE_BOOLEAN);
    $showDots = (bool) filter_var(setting('slider_show_dots', '1'), FILTER_VALIDATE_BOOLEAN);
    $siteBannerUrl = \App\Support\SiteMedia::bannerUrl();
    $bannerLink = \App\Support\SiteMedia::bannerLink();
    $bannerAlt = \App\Support\SiteMedia::bannerAlt();
    $hasSlider = $sliderSlides->isNotEmpty();
    $hasBanner = (bool) $siteBannerUrl;
@endphp

@if($hasSlider)
    <div
        class="hero-media slider-container relative w-full overflow-hidden bg-black/40"
        role="region"
        aria-label="{{ __('home.slider_region') }}"
        data-slider-total="{{ $sliderSlides->count() }}"
        data-slider-duration="{{ $slideDuration }}"
    >
        @foreach($sliderSlides as $index => $slide)
            <a
                href="{{ $slide->link ?: '#' }}"
                class="slider-slide block w-full transition-opacity duration-500 {{ $index === 0 ? 'relative opacity-100 z-10' : 'absolute inset-x-0 top-0 opacity-0 z-0 pointer-events-none' }}"
                data-slider-index="{{ $index }}"
                @if($index > 0) tabindex="-1" @endif
                @if(! $slide->link) onclick="return false;" @endif
            >
                <img src="{{ storage_asset($slide->image) }}" alt="{{ $slide->title ?: '' }}" class="slider-img w-full h-auto block max-w-full" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                @if($slide->title || ! empty($slide->subtitle))
                    <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/75 to-transparent px-5 py-4 text-white">
                        @if($slide->title)<span class="font-semibold block text-lg">{{ $slide->title }}</span>@endif
                        @if(! empty($slide->subtitle))<span class="text-sm opacity-90">{{ $slide->subtitle }}</span>@endif
                    </span>
                @endif
            </a>
        @endforeach

        @if($showArrows && $sliderSlides->count() > 1)
            <button type="button" class="slider-prev absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-xl bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors" aria-label="{{ __('home.slider_prev') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button type="button" class="slider-next absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-xl bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors" aria-label="{{ __('home.slider_next') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        @endif

        @if($showDots && $sliderSlides->count() > 1)
            <div class="slider-dots absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2" role="tablist" aria-label="{{ __('home.slider_dots') }}">
                @foreach($sliderSlides as $index => $slide)
                    <button type="button" class="slider-dot w-3 h-3 rounded-full transition-colors {{ $index === 0 ? 'bg-white' : 'bg-white/50 hover:bg-white/70' }}" data-slider-dot="{{ $index }}" role="tab" aria-label="{{ __('home.slider_slide') }} {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </div>
    <script>
        (function () {
            var container = document.querySelector('.hero-media.slider-container');
            if (!container) return;
            var total = parseInt(container.getAttribute('data-slider-total'), 10) || 1;
            var duration = parseInt(container.getAttribute('data-slider-duration'), 10) || 5000;
            var current = 0;
            var paused = false;
            var slides = container.querySelectorAll('.slider-slide');
            var dots = container.querySelectorAll('.slider-dot');

            function fitHeight(slide) {
                var img = slide && slide.querySelector('.slider-img');
                if (!img) return;
                var apply = function () {
                    var h = img.offsetHeight || img.naturalHeight;
                    if (h > 0) {
                        container.style.height = h + 'px';
                    }
                };
                if (img.complete && img.naturalHeight) {
                    apply();
                } else {
                    img.addEventListener('load', apply, { once: true });
                }
            }

            function show(i) {
                current = (i + total) % total;
                slides.forEach(function (el, idx) {
                    var active = idx === current;
                    el.classList.toggle('opacity-100', active);
                    el.classList.toggle('z-10', active);
                    el.classList.toggle('relative', active);
                    el.classList.toggle('opacity-0', !active);
                    el.classList.toggle('z-0', !active);
                    el.classList.toggle('pointer-events-none', !active);
                    el.classList.toggle('absolute', !active);
                    el.classList.toggle('inset-x-0', !active);
                    el.classList.toggle('top-0', !active);
                    if (active) {
                        el.removeAttribute('tabindex');
                    } else {
                        el.setAttribute('tabindex', '-1');
                    }
                });
                dots.forEach(function (el, idx) {
                    el.classList.toggle('bg-white', idx === current);
                    el.classList.toggle('bg-white/50', idx !== current);
                });
                fitHeight(slides[current]);
            }

            fitHeight(slides[0]);
            window.addEventListener('resize', function () { fitHeight(slides[current]); });

            if (total > 1) {
                setInterval(function () { if (!paused) show(current + 1); }, duration);
            }
            container.addEventListener('mouseenter', function () { paused = true; });
            container.addEventListener('mouseleave', function () { paused = false; });
            container.querySelectorAll('.slider-prev').forEach(function (btn) {
                btn.addEventListener('click', function () { show(current - 1); });
            });
            container.querySelectorAll('.slider-next').forEach(function (btn) {
                btn.addEventListener('click', function () { show(current + 1); });
            });
            container.querySelectorAll('.slider-dot').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var i = parseInt(btn.getAttribute('data-slider-dot'), 10);
                    if (!isNaN(i)) show(i);
                });
            });
        })();
    </script>
@elseif($hasBanner)
    <div class="hero-media relative w-full overflow-hidden bg-black/40">
        @if($bannerLink)
            <a href="{{ $bannerLink }}" class="block w-full"{!! link_new_tab_attrs($bannerLink) !!}>
                <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full h-auto block max-w-full">
            </a>
        @else
            <img src="{{ $siteBannerUrl }}" alt="{{ $bannerAlt }}" class="w-full h-auto block max-w-full">
        @endif
    </div>
@endif

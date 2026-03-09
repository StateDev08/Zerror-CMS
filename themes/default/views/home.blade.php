@extends('theme::layouts.app')

@section('title', config('clan.name') . ' - ' . __('nav.home'))

@section('content')
    @php
        $sliderSlides = \App\Models\SliderSlide::where('active', true)->orderBy('order')->get();
        $slideDuration = max(2, min(30, (int) setting('slider_duration_seconds', 5))) * 1000;
        $sliderHeightClass = match(setting('slider_height', 'medium')) {
            'small' => 'h-40 sm:h-52',
            'large' => 'h-72 sm:h-96 md:h-[28rem]',
            default => 'h-56 sm:h-72 md:h-80',
        };
        $showArrows = (bool) filter_var(setting('slider_show_arrows', '1'), FILTER_VALIDATE_BOOLEAN);
        $showDots = (bool) filter_var(setting('slider_show_dots', '1'), FILTER_VALIDATE_BOOLEAN);
    @endphp
    @if($sliderSlides->isNotEmpty())
        <div class="slider-container relative w-full {{ $sliderHeightClass }} rounded-2xl overflow-hidden mb-10 bg-neutral-200 dark:bg-neutral-800 shadow-lg" role="region" aria-label="{{ __('home.slider_region') }}" data-slider-total="{{ $sliderSlides->count() }}" data-slider-duration="{{ $slideDuration }}">
            @foreach($sliderSlides as $index => $slide)
                <a href="{{ $slide->link ?: '#' }}" class="slider-slide absolute inset-0 block transition-opacity duration-500 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}" data-slider-index="{{ $index }}" @if($index > 0) tabindex="-1" @endif>
                    <img src="{{ storage_asset($slide->image) }}" alt="{{ $slide->title ?: '' }}" class="w-full h-full object-cover">
                    @if($slide->title || $slide->subtitle ?? null)
                        <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent px-4 py-3 text-white">
                            @if($slide->title)<span class="font-medium block">{{ $slide->title }}</span>@endif
                            @if(!empty($slide->subtitle))<span class="text-sm opacity-90">{{ $slide->subtitle }}</span>@endif
                        </span>
                    @endif
                </a>
            @endforeach
            @if($showArrows && $sliderSlides->count() > 1)
                <button type="button" class="slider-prev absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-xl bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="{{ __('home.slider_prev') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="slider-next absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-xl bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="{{ __('home.slider_next') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif
            @if($showDots && $sliderSlides->count() > 1)
                <div class="slider-dots absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2" role="tablist" aria-label="{{ __('home.slider_dots') }}">
                    @foreach($sliderSlides as $index => $slide)
                        <button type="button" class="slider-dot w-3 h-3 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white/50 {{ $index === 0 ? 'bg-white' : 'bg-white/50 hover:bg-white/70' }}" data-slider-dot="{{ $index }}" role="tab" aria-label="{{ __('home.slider_slide') }} {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
        <script>
            (function() {
                var container = document.querySelector('.slider-container');
                if (!container) return;
                var total = parseInt(container.getAttribute('data-slider-total'), 10) || 1;
                var duration = parseInt(container.getAttribute('data-slider-duration'), 10) || 5000;
                var current = 0;
                var paused = false;
                var slides = container.querySelectorAll('.slider-slide');
                var dots = container.querySelectorAll('.slider-dot');
                function show(i) {
                    current = (i + total) % total;
                    slides.forEach(function(el, idx) {
                        var active = idx === current;
                        el.classList.toggle('opacity-100', active);
                        el.classList.toggle('z-10', active);
                        el.classList.toggle('opacity-0', !active);
                        el.classList.toggle('z-0', !active);
                        el.classList.toggle('pointer-events-none', !active);
                    });
                    dots.forEach(function(el, idx) {
                        el.classList.toggle('bg-white', idx === current);
                        el.classList.toggle('bg-white/50', idx !== current);
                    });
                }
                var interval = setInterval(function() {
                    if (!paused) show(current + 1);
                }, duration);
                container.addEventListener('mouseenter', function() { paused = true; });
                container.addEventListener('mouseleave', function() { paused = false; });
                container.querySelectorAll('.slider-prev').forEach(function(btn) {
                    btn.addEventListener('click', function() { show(current - 1); });
                });
                container.querySelectorAll('.slider-next').forEach(function(btn) {
                    btn.addEventListener('click', function() { show(current + 1); });
                });
                container.querySelectorAll('.slider-dot').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var i = parseInt(btn.getAttribute('data-slider-dot'), 10);
                        if (!isNaN(i)) show(i);
                    });
                });
            })();
        </script>
    @endif
    <div class="rounded-2xl border border-neutral-200/80 dark:border-neutral-700/80 bg-white dark:bg-neutral-900/60 p-6 md:p-8 shadow-sm">
        <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-neutral-100 tracking-tight">{{ __('home.welcome') }}</h1>
        <p class="mt-3 text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ __('home.intro') }}</p>
    </div>
    <div class="mt-10 grid gap-5 md:grid-cols-2">
        {!! app(\App\Support\WidgetRenderer::class)->slot('home') !!}
    </div>
@endsection

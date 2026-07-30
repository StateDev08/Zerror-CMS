<header class="sticky top-0 z-50 border-b border-white/10 bg-black/85 backdrop-blur-md" style="overflow:visible">
    <div class="w-full px-4 md:px-6 h-12 md:h-14 flex items-center gap-2 md:gap-5" style="overflow:visible">
        @include('theme::partials.mobile-nav')
        <div class="flex items-center gap-3 md:gap-5 min-w-0 flex-1" style="overflow:visible">
            <a href="{{ url('/') }}" class="flex items-center shrink-0" data-same-tab style="height:2.5rem;max-height:2.5rem;overflow:visible">
                @include('theme::partials.site-brand', ['variant' => 'game'])
            </a>
            <nav class="top-nav hidden md:flex items-center gap-4 lg:gap-6 min-w-0 overflow-x-auto">
                @include('theme::partials.top-nav-links')
            </nav>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @includeIf('mod_google_translate::nav')
            @include('theme::partials.user-menu')
        </div>
    </div>
    @include('theme::partials.mobile-nav-panel')
</header>

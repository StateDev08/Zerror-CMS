<header class="gs-top sticky top-0 z-50">
    <div class="gs-top__inner">
        @include('theme::partials.mobile-nav')
        <a href="{{ url('/') }}" class="gs-brand flex items-center shrink-0" data-same-tab style="height:2.5rem;max-height:2.5rem;overflow:visible">
            @include('theme::partials.site-brand', ['variant' => $brandVariant ?? 'game'])
        </a>
        <nav class="top-nav hidden md:flex items-center gap-4 lg:gap-6 min-w-0 flex-1 overflow-x-auto justify-center" aria-label="Hauptnavigation">
            @include('theme::partials.top-nav-links')
        </nav>
        <div class="gs-top__tools flex items-center gap-2 shrink-0">
            @includeIf('mod_google_translate::nav')
            @include('theme::partials.user-menu')
        </div>
    </div>
    @include('theme::partials.mobile-nav-panel')
</header>

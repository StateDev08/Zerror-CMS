<header class="mm-top sticky top-0 z-50">
    <div class="mm-top__crest" aria-hidden="true"></div>
    <div class="mm-top__inner">
        @include('theme::partials.mobile-nav')
        <a href="{{ url('/') }}" class="mm-brand" data-same-tab>
            @include('theme::partials.site-brand', ['variant' => 'game'])
        </a>
        <nav class="top-nav mm-nav mm-nav-scroll" aria-label="Hauptnavigation">
            @include('theme::partials.top-nav-links')
        </nav>
        <div class="mm-top__tools">
            @includeIf('mod_google_translate::nav')
            @include('theme::partials.user-menu')
        </div>
    </div>
    @include('theme::partials.mobile-nav-panel')
</header>

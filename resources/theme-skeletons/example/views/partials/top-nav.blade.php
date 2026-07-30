<header class="sticky top-0 z-50 border-b border-white/10 bg-black/85 backdrop-blur-md">
    <div class="max-w-[1400px] mx-auto px-4 md:px-6 h-12 md:h-14 flex items-center gap-4">
        <a href="{{ url('/') }}" class="flex items-center shrink-0" data-same-tab>
            @include('theme::partials.site-brand', ['variant' => 'game'])
        </a>
        <nav class="top-nav hidden md:flex flex-1 items-center justify-center gap-5 lg:gap-7">
            @include('theme::partials.top-nav-links')
        </nav>
        <div class="ml-auto flex items-center gap-3 text-xs uppercase tracking-wider" style="color: var(--theme-muted)">
            @auth
                <a href="{{ route('usercp.index') }}" class="hover:text-white" data-same-tab>{{ __('nav.usercp') }}</a>
                <a href="{{ url('/admin') }}" target="_blank" rel="noopener noreferrer" class="hover:text-white">{{ __('nav.admin') }}</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button class="hover:text-white uppercase tracking-wider">{{ __('auth.logout') }}</button></form>
            @else
                <a href="{{ route('login') }}" class="hover:text-white" data-same-tab>{{ __('auth.login') }}</a>
            @endauth
        </div>
    </div>
</header>

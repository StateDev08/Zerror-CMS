@php
    $brand = site_name();
@endphp
<header class="bb-nav sticky top-0 z-50">
    <div class="w-full px-4 md:px-6 h-13 md:h-14 flex items-center gap-3 md:gap-5" style="min-height:3.5rem">
        <div class="flex items-center gap-3 md:gap-5 min-w-0 flex-1">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0 group" data-same-tab>
                @include('theme::partials.site-brand', ['variant' => 'bluebyte'])
            </a>
            <nav class="top-nav hidden md:flex items-center gap-4 lg:gap-6 min-w-0 overflow-x-auto">
                @include('theme::partials.top-nav-links')
            </nav>
        </div>
        @include('theme::partials.user-menu')
    </div>
</header>

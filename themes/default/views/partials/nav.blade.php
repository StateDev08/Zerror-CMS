{{-- Vertikale Navigation mit aufklappbaren Kategorien --}}
@php
    $linkClass = $navLinkClass ?? 'text-neutral-700 dark:text-neutral-300 hover:text-[var(--theme-primary)] dark:hover:text-[var(--theme-primary)]';
    $dropdownBg = $navDropdownBg ?? 'bg-neutral-50/80 dark:bg-neutral-800/50';
    $dropdownHover = $navDropdownItemHover ?? 'hover:bg-neutral-100/80 dark:hover:bg-neutral-700/50';
@endphp
<div id="nav-menu" class="block">
    <ul class="flex flex-col gap-1">
        <li><a href="{{ url('/') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} transition-colors font-medium">{{ __('nav.home') }}</a></li>
        <li><a href="{{ route('news.index') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} transition-colors font-medium">{{ __('nav.news') }}</a></li>
        <li><a href="{{ route('roster.index') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} transition-colors font-medium">{{ __('nav.roster') }}</a></li>
        <li><a href="{{ route('calendar.index') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} transition-colors font-medium">{{ __('nav.calendar') }}</a></li>

        <li>
            <details class="group/details">
                <summary class="list-none cursor-pointer py-2.5 px-3 rounded-xl {{ $linkClass }} flex items-center justify-between font-medium transition-colors [&::-webkit-details-marker]:hidden">
                    <span>{{ __('nav.group_clan') }}</span>
                    <svg class="w-4 h-4 transition-transform group-open/details:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <ul class="mt-1 ml-2 pl-3 border-l-2 border-neutral-200 dark:border-neutral-600 {{ $dropdownBg }} rounded-r-xl space-y-0.5 py-2">
                    <li><a href="{{ route('apply.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.apply') }}</a></li>
                    <li><a href="{{ route('crafting.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.crafting') }}</a></li>
                    <li><a href="{{ route('clan-teams.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_teams') }}</a></li>
                    <li><a href="{{ route('clan-bank.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_bank') }}</a></li>
                    <li><a href="{{ route('clan-rules.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_rules') }}</a></li>
                    <li><a href="{{ route('clan-leaderboard.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_leaderboard') }}</a></li>
                    <li><a href="{{ route('clan-documents.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_documents') }}</a></li>
                    <li><a href="{{ route('clan-feedback.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_feedback') }}</a></li>
                    <li><a href="{{ route('clan-announcements.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_announcements') }}</a></li>
                    <li><a href="{{ route('clan-achievements.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.clan_achievements') }}</a></li>
                </ul>
            </details>
        </li>

        <li>
            <details class="group/details">
                <summary class="list-none cursor-pointer py-2.5 px-3 rounded-xl {{ $linkClass }} flex items-center justify-between font-medium transition-colors [&::-webkit-details-marker]:hidden">
                    <span>{{ __('nav.group_community') }}</span>
                    <svg class="w-4 h-4 transition-transform group-open/details:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <ul class="mt-1 ml-2 pl-3 border-l-2 border-neutral-200 dark:border-neutral-600 {{ $dropdownBg }} rounded-r-xl space-y-0.5 py-2">
                    <li><a href="{{ route('gallery.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.gallery') }}</a></li>
                    <li><a href="{{ route('downloads.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.downloads') }}</a></li>
                    <li><a href="{{ route('partners.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.partners') }}</a></li>
                    <li><a href="{{ route('polls.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.polls') }}</a></li>
                    <li><a href="{{ route('newsletter.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.newsletter') }}</a></li>
                    <li><a href="{{ route('wiki.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.wiki') }}</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.marketplace') }}</a></li>
                    <li><a href="{{ route('jobs.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.jobs') }}</a></li>
                    @if(\Illuminate\Support\Facades\Route::has('forum.index'))
                        <li><a href="{{ route('forum.index') }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.forum') }}</a></li>
                    @endif
                </ul>
            </details>
        </li>

        <li>
            <details class="group/details">
                <summary class="list-none cursor-pointer py-2.5 px-3 rounded-xl {{ $linkClass }} flex items-center justify-between font-medium transition-colors [&::-webkit-details-marker]:hidden">
                    <span>{{ __('nav.group_legal') }}</span>
                    <svg class="w-4 h-4 transition-transform group-open/details:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <ul class="mt-1 ml-2 pl-3 border-l-2 border-neutral-200 dark:border-neutral-600 {{ $dropdownBg }} rounded-r-xl space-y-0.5 py-2">
                    <li><a href="{{ route('page.show', ['slug' => 'impressum']) }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.impressum') }}</a></li>
                    <li><a href="{{ route('page.show', ['slug' => 'datenschutz']) }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.datenschutz') }}</a></li>
                    <li><a href="{{ route('page.show', ['slug' => 'agb']) }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.agb') }}</a></li>
                    <li><a href="{{ route('page.show', ['slug' => 'cookies']) }}" class="block py-2 px-3 rounded-lg {{ $dropdownHover }} {{ $linkClass }} text-sm transition-colors">{{ __('nav.cookies') }}</a></li>
                </ul>
            </details>
        </li>

        @guest
            <li><a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} font-medium transition-colors">{{ __('auth.login') }}</a></li>
            @if((bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN))
                <li><a href="{{ route('register') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} font-medium transition-colors">{{ __('auth.register') }}</a></li>
            @endif
        @else
            <li><a href="{{ route('usercp.index') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} font-medium transition-colors">{{ __('nav.usercp') }}</a></li>
            <li><a href="{{ url('/admin') }}" class="block py-2.5 px-3 rounded-xl {{ $linkClass }} font-semibold transition-colors">{{ __('nav.admin') }}</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="block py-2.5 px-3 rounded-xl w-full text-left {{ $linkClass }} font-medium transition-colors">{{ __('auth.logout') }}</button>
                </form>
            </li>
        @endguest
    </ul>
</div>

@php
    $linkClass = $navLinkClass ?? 'text-neutral-700 dark:text-neutral-300 hover:text-[var(--theme-primary)] dark:hover:text-[var(--theme-primary)]';
@endphp
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

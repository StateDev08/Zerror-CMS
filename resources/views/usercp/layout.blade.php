@extends('theme::layouts.app')

@section('title', config('clan.name') . ' - ' . __('nav.usercp'))

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    <nav class="md:w-56 shrink-0 p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <ul class="space-y-1">
            <li><a href="{{ route('usercp.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('usercp.index') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ __('usercp.overview') }}</a></li>
            <li><a href="{{ route('usercp.profile') }}" class="block px-3 py-2 rounded {{ request()->routeIs('usercp.profile') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ __('usercp.profile') }}</a></li>
            <li><a href="{{ route('usercp.password') }}" class="block px-3 py-2 rounded {{ request()->routeIs('usercp.password') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ __('usercp.password') }}</a></li>
            <li><a href="{{ route('usercp.item-requests') }}" class="block px-3 py-2 rounded {{ request()->routeIs('usercp.item-requests') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ __('crafting.my_requests') }}</a></li>
        </ul>
    </nav>
    <div class="flex-1 min-w-0">
        @if(session('success'))
            <p class="mb-4 p-3 rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</p>
        @endif
        @yield('usercp_content')
    </div>
</div>
@endsection

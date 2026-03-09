@extends('theme::layouts.app')

@section('title', ($profileUser->name ?? __('usercp.profile')) . ' - ' . config('clan.name'))

@section('content')
    <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 max-w-2xl">
        <div class="flex flex-wrap items-start gap-6">
            @if($profileUser->avatar_url)
                <img src="{{ $profileUser->avatar_url }}" alt="" class="h-24 w-24 rounded-full object-cover border border-gray-300 dark:border-gray-600 shrink-0">
            @else
                <div class="h-24 w-24 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-3xl font-semibold text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 shrink-0" aria-hidden="true">{{ Str::limit(mb_strtoupper(mb_substr($profileUser->name ?? 'U', 0, 2)), 2) }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $profileUser->name }}</h1>
                @if($profileUser->job)
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $profileUser->job }}</p>
                @endif
            </div>
        </div>
        @if($profileUser->biography)
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.biography') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $profileUser->biography }}</p>
            </div>
        @endif
        @if($profileUser->about_me)
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.about_me') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $profileUser->about_me }}</p>
            </div>
        @endif
        <dl class="mt-6 grid gap-2 sm:grid-cols-1">
            @if($profileUser->location)
                <div><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('usercp.location') }}</dt><dd class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $profileUser->location }}</dd></div>
            @endif
            @if($profileUser->website)
                <div><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('usercp.website') }}</dt><dd class="mt-0.5"><a href="{{ $profileUser->website }}" target="_blank" rel="noopener noreferrer" class="text-amber-600 dark:text-amber-400 hover:underline">{{ $profileUser->website }}</a></dd></div>
            @endif
            @if($profileUser->discord_handle)
                <div><dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('usercp.discord_handle') }}</dt><dd class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $profileUser->discord_handle }}</dd></div>
            @endif
        </dl>
    </div>
@endsection

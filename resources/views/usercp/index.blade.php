@extends('usercp.layout')

@section('usercp_content')
    <h1 class="text-2xl font-semibold mb-4">{{ __('usercp.overview') }}</h1>
    <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-wrap items-start gap-4">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="" class="h-20 w-20 rounded-full object-cover border border-gray-300 dark:border-gray-600 shrink-0">
            @else
                <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xl font-semibold text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 shrink-0" aria-hidden="true">{{ Str::limit(mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)), 2) }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 dark:text-gray-400">{{ __('usercp.welcome') }}, <strong>{{ $user->name }}</strong>.</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                @if($user->job)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('usercp.job') }}: {{ $user->job }}</p>
                @endif
                @if($user->about_me)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ str()->limit($user->about_me, 120) }}</p>
                @endif
            </div>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('usercp.profile') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium">{{ __('usercp.profile_edit') }}</a>
            @if(Route::has('profile.public'))
                <a href="{{ route('profile.public', ['user' => $user->id]) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium">{{ __('usercp.view_public_profile') }}</a>
            @endif
            <a href="{{ route('usercp.password') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium">{{ __('usercp.password') }} ändern</a>
        </div>
    </div>
@endsection

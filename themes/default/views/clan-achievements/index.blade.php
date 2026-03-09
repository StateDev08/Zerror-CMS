@extends('theme::layouts.app')

@section('title', __('nav.clan_achievements') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_achievements') }}</h1>
    @if($achievements->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.achievements_none') }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($achievements as $achievement)
                <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    @if($achievement->icon)
                        <span class="text-2xl mb-2 block">{{ $achievement->icon }}</span>
                    @endif
                    <h2 class="font-semibold text-lg">{{ $achievement->title }}</h2>
                    @if($achievement->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $achievement->description }}</p>
                    @endif
                    @if($achievement->achieved_at)
                        <p class="text-xs text-gray-400 mt-2">{{ __('clan.achieved_at') }} {{ $achievement->achieved_at->format(__('general.date_format')) }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection

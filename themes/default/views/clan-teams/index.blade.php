@extends('theme::layouts.app')

@section('title', __('nav.clan_teams') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_teams') }}</h1>
    @if($teams->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.teams_none') }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($teams as $team)
                <a href="{{ route('clan-teams.show', $team) }}" class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <h2 class="font-semibold text-lg mb-1">{{ $team->name }}</h2>
                    @if($team->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit(strip_tags($team->description), 80) }}</p>
                    @endif
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">{{ $team->members->count() }} {{ __('clan.members') }}</p>
                </a>
            @endforeach
        </div>
    @endif
@endsection

@extends('theme::layouts.app')

@section('title', __('nav.clan_leaderboard') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_leaderboard') }}</h1>
    @if($categories->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.leaderboard_empty') }}</p>
    @else
        @foreach($categories as $cat)
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-2">{{ $cat->name }}@if($cat->season) <span class="text-gray-500 dark:text-gray-400">({{ $cat->season }})</span>@endif</h2>
                @if($cat->entries->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('clan.no_entries') }}</p>
                @else
                    <ul class="space-y-1 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        @foreach($cat->entries as $index => $entry)
                            <li class="flex items-center gap-4 p-3 {{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                                <span class="w-8 font-bold text-gray-500">{{ $index + 1 }}.</span>
                                <span class="font-medium">{{ $entry->player_name }}</span>
                                <span class="ml-auto">{{ $entry->score }} {{ __('clan.points') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    @endif
@endsection

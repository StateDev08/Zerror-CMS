@extends('theme::layouts.app')

@section('title', $category->name . ' - ' . __('nav.clan_leaderboard'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4"><a href="{{ route('clan-leaderboard.index') }}" class="hover:underline">{{ __('nav.clan_leaderboard') }}</a> &rarr; {{ $category->name }}</nav>
    <h1 class="text-2xl font-bold mb-4">{{ $category->name }}@if($category->season) ({{ $category->season }})@endif</h1>
    @if($category->entries->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.no_entries') }}</p>
    @else
        <ul class="space-y-1 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            @foreach($category->entries as $index => $entry)
                <li class="flex items-center gap-4 p-3 {{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                    <span class="w-8 font-bold">{{ $index + 1 }}.</span>
                    <span class="font-medium">{{ $entry->player_name }}</span>
                    <span class="ml-auto">{{ $entry->score }} {{ __('clan.points') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

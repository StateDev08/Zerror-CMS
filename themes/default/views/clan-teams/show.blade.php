@extends('theme::layouts.app')

@section('title', $team->name . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4"><a href="{{ route('clan-teams.index') }}" class="hover:underline">{{ __('nav.clan_teams') }}</a> &rarr; {{ $team->name }}</nav>
    <h1 class="text-2xl font-bold mb-2">{{ $team->name }}</h1>
    @if($team->contact)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('clan.contact') }}: {{ $team->contact }}</p>
    @endif
    @if($team->description)
        <div class="prose dark:prose-invert max-w-none mb-6">{!! nl2br(e($team->description)) !!}</div>
    @endif
    <h2 class="text-lg font-semibold mb-3">{{ __('clan.team_members') }}</h2>
    @if($team->members->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.no_members') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($team->members as $m)
                <li class="flex items-center gap-2">
                    <span class="font-medium">{{ $m->display_name }}</span>
                    @if($m->role)<span class="text-gray-500 dark:text-gray-400">– {{ $m->role }}</span>@endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection

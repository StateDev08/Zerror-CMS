@extends('theme::layouts.app')

@section('title', __('nav.clan_announcements') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_announcements') }}</h1>
    @if($announcements->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.announcements_none') }}</p>
    @else
        <div class="space-y-6">
            @foreach($announcements as $announcement)
                <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $announcement->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $announcement->created_at->format(__('general.date_format')) }}@if($announcement->visible_until) · {{ __('clan.visible_until') }} {{ $announcement->visible_until->format(__('general.date_format')) }}@endif</p>
                    <div class="prose dark:prose-invert max-w-none">{!! $announcement->body !!}</div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $announcements->links() }}</div>
    @endif
@endsection

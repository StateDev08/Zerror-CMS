@extends('theme::layouts.app')

@section('title', $forum->name . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('forum.index') }}" class="hover:underline">{{ __('nav.forum') }}</a> / {{ $forum->name }}
    </nav>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">{{ $forum->name }}</h1>
        @auth
            @if(!$forum->write_rank_id || auth()->user()->clanMember?->rank_id)
                <a href="{{ route('forum.thread.create', $forum) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('forum.new_thread') }}</a>
            @endif
        @endauth
    </div>
    @if($forum->description)
        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $forum->description }}</p>
    @endif
    @if($threads->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('forum.no_threads') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($threads as $thread)
                <li class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2">
                        @if($thread->pinned)
                            <span class="text-amber-600">📌</span>
                        @endif
                        <a href="{{ route('forum.thread.show', $thread) }}" class="font-medium hover:underline">{{ $thread->title }}</a>
                    </div>
                    <span class="text-sm text-gray-500">{{ $thread->posts_count }} {{ __('forum.posts') }} · {{ $thread->updated_at->format(__('general.date_format')) }}</span>
                </li>
            @endforeach
        </ul>
        {{ $threads->links() }}
    @endif
@endsection

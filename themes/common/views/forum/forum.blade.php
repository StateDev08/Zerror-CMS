@extends('theme::layouts.app')

@section('title', $forum->name . ' - ' . site_name())

@section('content')
    @php
        $user = auth()->user();
        $isAdmin = $user && ($user->can('access_admin') || $user->hasRole('super-admin'));
        $canCreate = false;
        if ($user) {
            if ($isAdmin || ! $forum->write_rank_id) {
                $canCreate = true;
            } else {
                $userRank = $user->clanMember?->rank;
                $forumRank = $forum->writeRank;
                $canCreate = $userRank && $forumRank && $userRank->order <= $forumRank->order;
            }
        }
    @endphp

    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('forum.index') }}" class="theme-link-primary hover:underline">{{ __('nav.forum') }}</a>
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ $forum->name }}</span>
    </nav>

    <header class="clan-frame panel-box rounded-xl p-5 mb-6 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <h1 class="font-display text-2xl font-semibold mb-2" style="color: var(--theme-text);">{{ $forum->name }}</h1>
            @if($forum->description)
                <div class="text-sm leading-relaxed" style="color: var(--theme-muted);">
                    {!! \App\Support\HtmlContent::toHtml($forum->description) !!}
                </div>
            @endif
        </div>
        @if($canCreate)
            <a href="{{ route('forum.thread.create', $forum) }}" class="theme-bg-primary inline-flex shrink-0 px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('forum.new_thread') }}</a>
        @endif
    </header>

    @if($threads->isEmpty())
        <div class="clan-frame panel-box rounded-xl px-5 py-8 text-center max-w-lg mx-auto">
            <p class="font-display text-lg mb-2" style="color: var(--theme-text);">{{ __('forum.no_threads') }}</p>
            <p class="text-sm mb-5" style="color: var(--theme-muted);">{{ __('forum.no_threads_hint') }}</p>
            @if($canCreate)
                <a href="{{ route('forum.thread.create', $forum) }}" class="theme-bg-primary inline-flex px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('forum.new_thread') }}</a>
            @endif
        </div>
    @else
        <div class="grid gap-3">
            @foreach($threads as $thread)
                <a href="{{ route('forum.thread.show', $thread) }}" class="clan-frame panel-box rounded-xl px-5 py-4 flex flex-wrap items-center justify-between gap-3 no-underline hover:opacity-95" style="color: inherit;">
                    <div class="min-w-0 flex flex-wrap items-center gap-2 flex-1">
                        @if($thread->pinned)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded shrink-0" style="background: color-mix(in srgb, var(--theme-primary) 22%, transparent); color: var(--theme-primary);">{{ __('forum.pinned_badge') }}</span>
                        @endif
                        @if($thread->locked)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded shrink-0" style="background: color-mix(in srgb, #dc2626 22%, transparent); color: #fca5a5;">{{ __('forum.locked_badge') }}</span>
                        @endif
                        <span class="font-display font-semibold text-base truncate" style="color: var(--theme-text);">{{ $thread->title }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm shrink-0" style="color: var(--theme-muted);">
                        <span>{{ $thread->user?->name ?? __('forum.guest') }}</span>
                        <span style="color: var(--theme-primary);">{{ $thread->posts_count }} {{ __('forum.posts') }}</span>
                        <span>{{ $thread->updated_at->timezone(config('app.timezone'))->format(site_date_format()) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $threads->links() }}</div>
    @endif
@endsection

@extends('theme::layouts.app')

@section('title', $thread->title . ' - ' . site_name())

@section('content')
    @php
        $isAdmin = auth()->check() && (auth()->user()->can('access_admin') || auth()->user()->hasRole('super-admin'));
        $canReply = auth()->check() && (! $thread->locked || $isAdmin);
    @endphp
    <nav class="text-sm mb-4" style="color: var(--theme-muted);">
        <a href="{{ route('forum.index') }}" class="theme-link-primary hover:underline">{{ __('nav.forum') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('forum.show', $thread->forum) }}" class="theme-link-primary hover:underline">{{ $thread->forum->name }}</a>
        <span class="mx-1">/</span>
        <span style="color: var(--theme-text);">{{ $thread->title }}</span>
    </nav>

    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title mb-2">
                {{ $thread->title }}
                @if($thread->locked)
                    <span class="ml-2 align-middle text-xs font-semibold px-2 py-1 rounded" style="background: color-mix(in srgb, #dc2626 22%, transparent); color: #fca5a5;">{{ __('forum.locked_badge') }}</span>
                @endif
                @if($thread->pinned)
                    <span class="ml-2 align-middle text-xs font-semibold px-2 py-1 rounded" style="background: color-mix(in srgb, var(--theme-primary) 22%, transparent); color: var(--theme-primary);">{{ __('forum.pinned_badge') }}</span>
                @endif
            </h1>
        </div>
        @auth
            <div class="flex flex-wrap gap-2 text-sm">
                @if(auth()->id() === $thread->user_id || $isAdmin)
                    @if(! $thread->locked || $isAdmin)
                        <a href="{{ route('forum.thread.edit', $thread) }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text);">{{ __('forum.edit') }}</a>
                        <form action="{{ route('forum.thread.destroy', $thread) }}" method="POST" onsubmit="return confirm('{{ __('forum.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, #dc2626 50%, transparent); color: #fca5a5;">{{ __('forum.delete') }}</button>
                        </form>
                    @endif
                @endif
                @if($isAdmin)
                    <form action="{{ route('forum.thread.toggle-pin', $thread) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text);">{{ $thread->pinned ? __('forum.unpin') : __('forum.pin') }}</button>
                    </form>
                    <form action="{{ route('forum.thread.toggle-lock', $thread) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text);">{{ $thread->locked ? __('forum.unlock') : __('forum.lock') }}</button>
                    </form>
                @endif
            </div>
        @endauth
    </div>

    @if(session('success'))
        <p class="mb-4 p-3 rounded-xl text-sm" style="background: color-mix(in srgb, #16a34a 18%, transparent); color: #bbf7d0;">{{ session('success') }}</p>
    @endif

    <div class="grid gap-4">
        @foreach($posts as $post)
            @php
                $author = $post->user;
                $clan = $author?->clanMember;
                $visibleClan = $clan && $clan->visible ? $clan : null;
                $authorName = $visibleClan?->display_name ?: ($author?->name ?? __('forum.guest'));
                $authorAvatar = ($visibleClan?->avatar ? storage_asset($visibleClan->avatar) : null) ?: $author?->avatar_url;
                $authorRank = $visibleClan?->rank?->name
                    ?? ($author?->getRoleNames()->map(fn ($n) => ucwords(str_replace('-', ' ', (string) $n)))->first());
                $rankColor = $visibleClan?->rank?->color;
            @endphp
            <article id="post-{{ $post->id }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    @if($authorAvatar)
                        <img src="{{ $authorAvatar }}" alt="" class="w-14 h-14 rounded-xl object-cover shrink-0" style="box-shadow: 0 0 0 1px color-mix(in srgb, var(--theme-primary) 35%, transparent);">
                    @else
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center font-semibold text-xl shrink-0" style="background: color-mix(in srgb, var(--theme-surface) 70%, #000); color: var(--theme-muted); box-shadow: 0 0 0 1px color-mix(in srgb, var(--theme-primary) 25%, transparent);">
                            {{ mb_substr($authorName, 0, 1) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h2 class="font-display font-semibold text-lg truncate" style="color: var(--theme-text);">{{ $authorName }}</h2>
                        <dl class="grid gap-1 text-sm mt-1">
                            <div class="flex items-baseline justify-between gap-3">
                                <dt style="color: var(--theme-muted);">{{ __('clan.rank') }}</dt>
                                <dd class="font-semibold text-right" style="color: {{ $rankColor ?: 'var(--theme-primary)' }};">{{ $authorRank ?: __('clan.rank_none') }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-3">
                                <dt style="color: var(--theme-muted);">{{ __('forum.posted_at') }}</dt>
                                <dd class="text-right" style="color: var(--theme-text);">{{ $post->created_at->timezone(config('app.timezone'))->format(site_datetime_format()) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="prose dark:prose-invert max-w-none" style="color: var(--theme-text);">
                    {!! \App\Support\ForumFormat::bodyToHtml($post->body) !!}
                </div>

                @if($canReply || (auth()->check() && (auth()->id() === $post->user_id || $isAdmin) && (! $thread->locked || $isAdmin)))
                    <div class="flex flex-wrap gap-3 text-sm pt-1" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 20%, transparent);">
                        @if($canReply)
                            @php
                                $quotePlain = trim(html_entity_decode(strip_tags($post->body), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                            @endphp
                            <button type="button" class="forum-quote-btn font-semibold" style="color: var(--theme-primary);" data-quote-author="{{ e($authorName) }}" data-quote-body="{{ e($quotePlain) }}">{{ __('forum.quote') }}</button>
                        @endif
                        @if(auth()->check() && (auth()->id() === $post->user_id || $isAdmin) && (! $thread->locked || $isAdmin))
                            <a href="{{ route('forum.post.edit', $post) }}" class="font-semibold" style="color: var(--theme-primary);">{{ __('forum.edit') }}</a>
                            <form action="{{ route('forum.post.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('forum.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-semibold" style="color: #fca5a5;">{{ __('forum.delete') }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>

    @if($canReply)
        <form id="reply-form" action="{{ route('forum.thread.reply', $thread) }}" method="POST" class="mt-6 clan-frame panel-box rounded-xl p-5">
            @csrf
            <label class="block font-semibold mb-2" style="color: var(--theme-text);">{{ __('forum.reply') }}</label>
            <livewire:cms-rich-editor-field name="body" :value="old('body')" :compact="true" :listen-quotes="true" />
            @error('body')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
            <button type="submit" class="theme-bg-primary mt-3 inline-flex px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('forum.submit_reply') }}</button>
        </form>
        <script>
            document.querySelectorAll('.forum-quote-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var author = this.getAttribute('data-quote-author') || '';
                    var body = this.getAttribute('data-quote-body') || '';
                    if (window.Livewire) {
                        window.Livewire.dispatch('forum-quote', { author: author, body: body });
                    }
                    document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
                });
            });
        </script>
    @elseif($thread->locked)
        <p class="mt-6 text-sm" style="color: var(--theme-muted);">{{ __('forum.thread_locked') }}</p>
    @endif
@endsection

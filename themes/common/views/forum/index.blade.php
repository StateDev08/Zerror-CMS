@extends('theme::layouts.app')

@section('title', __('nav.forum') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.forum') }}</h1>
    @if($categories->isEmpty())
        <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl mx-auto">
            <p style="color: var(--theme-muted);">{{ __('forum.no_forums') }}</p>
        </div>
    @else
        @foreach($categories as $category)
            <section class="mb-10">
                <h2 class="font-display text-xl font-semibold mb-4" style="color: var(--theme-primary);">{{ $category->name }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($category->forums as $forum)
                        @php
                            $descPlain = trim(preg_replace('/\s+/', ' ', strip_tags((string) $forum->description)));
                        @endphp
                        <a href="{{ route('forum.show', $forum) }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-3 no-underline transition-opacity hover:opacity-95" style="color: inherit;">
                            <h3 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">{{ $forum->name }}</h3>
                            @if($descPlain !== '')
                                <p class="text-sm leading-relaxed" style="color: var(--theme-muted);">{{ \Illuminate\Support\Str::limit($descPlain, 120) }}</p>
                            @endif
                            <div class="flex items-center justify-between gap-3 text-sm mt-auto pt-2" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                                <span style="color: var(--theme-muted);">{{ __('forum.threads') }}</span>
                                <span class="font-semibold" style="color: var(--theme-primary);">{{ $forum->threads_count ?? $forum->threads()->count() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
@endsection

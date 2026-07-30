@extends('theme::layouts.app')

@section('title', __('nav.news') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.news') }}</h1>

    @if($posts->isEmpty())
        <div class="clan-frame panel-box rounded-xl px-5 py-8 text-center max-w-xl mx-auto">
            <p style="color: var(--theme-muted);">{{ __('widgets.no_news') }}</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($posts as $post)
                @php
                    $excerpt = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->content)));
                @endphp
                <a href="{{ route('news.show', $post->slug) }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-3 no-underline transition-opacity hover:opacity-95" style="color: inherit;">
                    <h2 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">{{ $post->title }}</h2>
                    @if($excerpt !== '')
                        <p class="text-sm leading-relaxed" style="color: var(--theme-muted);">{{ \Illuminate\Support\Str::limit($excerpt, 140) }}</p>
                    @endif
                    <div class="flex items-center justify-between gap-3 text-sm mt-auto pt-2" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                        <span style="color: var(--theme-muted);">{{ __('news.published') }}</span>
                        <span class="font-semibold" style="color: var(--theme-primary);">{{ $post->created_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection

@extends('theme::layouts.app')

@section('title', $post->title . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('news.index') }}" class="theme-link-primary hover:underline">{{ __('nav.news') }}</a>
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ $post->title }}</span>
    </nav>

    <article class="clan-frame panel-box rounded-xl p-5 md:p-6">
        <h1 class="font-display text-2xl font-semibold mb-2" style="color: var(--theme-text);">{{ $post->title }}</h1>
        <p class="text-sm mb-5" style="color: var(--theme-muted);">
            {{ __('news.published') }}
            <span style="color: var(--theme-primary);">{{ $post->created_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</span>
            @if($post->author)
                · {{ $post->author->name }}
            @endif
        </p>
        @if($post->content)
            <div class="text-sm leading-relaxed" style="color: var(--theme-text);">
                {!! \App\Support\HtmlContent::toHtml($post->content) !!}
            </div>
        @endif
    </article>
@endsection

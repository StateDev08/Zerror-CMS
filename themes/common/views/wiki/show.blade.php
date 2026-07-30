@extends('theme::layouts.app')

@section('title', $article->title . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('wiki.index') }}" class="theme-link-primary hover:underline">{{ __('nav.wiki') }}</a>
        @if($article->category)
            <span class="mx-1 opacity-60">/</span>
            <a href="{{ route('wiki.category', $article->category) }}" class="theme-link-primary hover:underline">{{ $article->category->name }}</a>
        @endif
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ $article->title }}</span>
    </nav>

    <article class="clan-frame panel-box rounded-xl p-5 md:p-6">
        <h1 class="font-display text-2xl font-semibold mb-3" style="color: var(--theme-text);">{{ $article->title }}</h1>
        <p class="text-sm mb-5" style="color: var(--theme-muted);">{{ __('wiki.updated') }} {{ $article->updated_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</p>
        @if($article->body)
            <div class="text-sm leading-relaxed" style="color: var(--theme-text);">
                {!! \App\Support\HtmlContent::toHtml($article->body) !!}
            </div>
        @endif
    </article>
@endsection

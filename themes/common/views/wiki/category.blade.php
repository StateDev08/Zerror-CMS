@extends('theme::layouts.app')

@section('title', $category->name . ' - ' . __('nav.wiki') . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('wiki.index') }}" class="theme-link-primary hover:underline">{{ __('nav.wiki') }}</a>
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ $category->name }}</span>
    </nav>

    <header class="clan-frame panel-box rounded-xl p-5 mb-6">
        <h1 class="font-display text-2xl font-semibold mb-2" style="color: var(--theme-text);">{{ $category->name }}</h1>
        @if($category->description)
            <div class="text-sm leading-relaxed" style="color: var(--theme-muted);">
                {!! \App\Support\HtmlContent::toHtml($category->description) !!}
            </div>
        @endif
    </header>

    @if($articles->isEmpty())
        <div class="clan-frame panel-box rounded-xl px-5 py-8 text-center max-w-lg mx-auto">
            <p class="font-display text-lg mb-2" style="color: var(--theme-text);">{{ __('wiki.no_articles') }}</p>
        </div>
    @else
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($articles as $article)
                <a href="{{ route('wiki.show', $article->slug) }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-2 no-underline hover:opacity-95" style="color: inherit;">
                    <h2 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">{{ $article->title }}</h2>
                    <div class="flex items-center justify-between gap-3 text-sm mt-auto pt-2" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                        <span style="color: var(--theme-muted);">{{ __('wiki.updated') }}</span>
                        <span class="font-semibold" style="color: var(--theme-primary);">{{ $article->updated_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

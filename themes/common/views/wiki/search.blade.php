@extends('theme::layouts.app')

@section('title', __('wiki.search_results') . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('wiki.index') }}" class="theme-link-primary hover:underline">{{ __('nav.wiki') }}</a>
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ __('wiki.search_results') }}</span>
    </nav>

    <h1 class="page-title mb-6">{{ __('wiki.search_results') }}</h1>

    <form action="{{ route('wiki.search') }}" method="get" class="clan-frame panel-box rounded-xl p-4 mb-8 flex flex-wrap gap-3 items-center">
        <input
            type="search"
            name="q"
            value="{{ $q }}"
            placeholder="{{ __('wiki.search_placeholder') }}"
            class="flex-1 min-w-[12rem] rounded-lg px-3 py-2.5 text-sm"
            style="background: color-mix(in srgb, var(--theme-surface) 80%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);"
        >
        <button type="submit" class="theme-bg-primary inline-flex px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('wiki.search') }}</button>
    </form>

    @if(strlen($q) < 2)
        <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
            <p style="color: var(--theme-muted);">{{ __('wiki.search_min') }}</p>
        </div>
    @elseif($articles->isEmpty())
        <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
            <p style="color: var(--theme-muted);">{{ __('wiki.no_results') }}</p>
        </div>
    @else
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($articles as $article)
                <a href="{{ route('wiki.show', $article->slug) }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-2 no-underline hover:opacity-95" style="color: inherit;">
                    <h2 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">{{ $article->title }}</h2>
                    <div class="flex items-center justify-between gap-3 text-sm mt-auto pt-2" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                        <span style="color: var(--theme-muted);">{{ $article->category?->name ?? __('wiki.categories') }}</span>
                        <span class="font-semibold" style="color: var(--theme-primary);">{{ $article->updated_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

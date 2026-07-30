@extends('theme::layouts.app')

@section('title', __('nav.wiki') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-6">{{ __('nav.wiki') }}</h1>

    <form action="{{ route('wiki.search') }}" method="get" class="clan-frame panel-box rounded-xl p-4 mb-8 flex flex-wrap gap-3 items-center">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="{{ __('wiki.search_placeholder') }}"
            class="flex-1 min-w-[12rem] rounded-lg px-3 py-2.5 text-sm"
            style="background: color-mix(in srgb, var(--theme-surface) 80%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);"
        >
        <button type="submit" class="theme-bg-primary inline-flex px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('wiki.search') }}</button>
    </form>

    <section class="mb-10">
        <h2 class="font-display text-xl font-semibold mb-4" style="color: var(--theme-primary);">{{ __('wiki.categories') }}</h2>
        @if($categories->isEmpty())
            <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
                <p style="color: var(--theme-muted);">{{ __('wiki.no_categories') }}</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($categories as $cat)
                    @php
                        $descPlain = trim(preg_replace('/\s+/', ' ', strip_tags((string) $cat->description)));
                    @endphp
                    <a href="{{ route('wiki.category', $cat) }}" class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-3 no-underline transition-opacity hover:opacity-95" style="color: inherit;">
                        <h3 class="font-display font-semibold text-lg leading-snug" style="color: var(--theme-text);">{{ $cat->name }}</h3>
                        @if($descPlain !== '')
                            <p class="text-sm leading-relaxed" style="color: var(--theme-muted);">{{ \Illuminate\Support\Str::limit($descPlain, 120) }}</p>
                        @endif
                        <div class="flex items-center justify-between gap-3 text-sm mt-auto pt-2" style="border-top: 1px solid color-mix(in srgb, var(--theme-primary) 18%, transparent);">
                            <span style="color: var(--theme-muted);">{{ __('wiki.articles') }}</span>
                            <span class="font-semibold" style="color: var(--theme-primary);">{{ $cat->articles_count }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <h2 class="font-display text-xl font-semibold mb-4" style="color: var(--theme-primary);">{{ __('wiki.recent') }}</h2>
        @if($recent->isEmpty())
            <div class="clan-frame panel-box rounded-xl px-5 py-6 text-center max-w-xl">
                <p style="color: var(--theme-muted);">{{ __('wiki.no_articles') }}</p>
            </div>
        @else
            <div class="grid gap-3">
                @foreach($recent as $article)
                    <a href="{{ route('wiki.show', $article->slug) }}" class="clan-frame panel-box rounded-xl px-5 py-4 flex flex-wrap items-center justify-between gap-3 no-underline hover:opacity-95" style="color: inherit;">
                        <span class="font-display font-semibold text-base truncate" style="color: var(--theme-text);">{{ $article->title }}</span>
                        <span class="text-sm shrink-0" style="color: var(--theme-muted);">{{ $article->updated_at->timezone(config('app.timezone'))->format(__('general.date_format')) }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection

@extends('theme::layouts.app')

@section('title', __('nav.wiki') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.wiki') }}</h1>
    <form action="{{ route('wiki.search') }}" method="get" class="mb-6">
        <div class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('wiki.search_placeholder') }}" class="flex-1 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-2">
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('wiki.search') }}</button>
        </div>
    </form>
    <div class="grid gap-8 md:grid-cols-2">
        <section>
            <h2 class="text-xl font-semibold mb-3">{{ __('wiki.categories') }}</h2>
            @if($categories->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">{{ __('wiki.no_categories') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('wiki.category', $cat) }}" class="hover:underline font-medium">{{ $cat->name }}</a>
                            <span class="text-sm text-gray-500 dark:text-gray-400">({{ $cat->articles_count }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
        <section>
            <h2 class="text-xl font-semibold mb-3">{{ __('wiki.recent') }}</h2>
            @if($recent->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">{{ __('wiki.no_articles') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach($recent as $article)
                        <li>
                            <a href="{{ route('wiki.show', $article->slug) }}" class="hover:underline">{{ $article->title }}</a>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $article->updated_at->format(__('general.date_format')) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection

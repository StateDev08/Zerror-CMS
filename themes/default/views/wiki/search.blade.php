@extends('theme::layouts.app')

@section('title', __('wiki.search_results') . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('wiki.index') }}" class="hover:underline">{{ __('nav.wiki') }}</a> / {{ __('wiki.search_results') }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ __('wiki.search_results') }}</h1>
    <form action="{{ route('wiki.search') }}" method="get" class="mb-6">
        <div class="flex gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('wiki.search_placeholder') }}" class="flex-1 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-2">
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('wiki.search') }}</button>
        </div>
    </form>
    @if(strlen($q) < 2)
        <p class="text-gray-500 dark:text-gray-400">{{ __('wiki.search_min') }}</p>
    @elseif($articles->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('wiki.no_results') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($articles as $article)
                <li>
                    <a href="{{ route('wiki.show', $article->slug) }}" class="hover:underline font-medium">{{ $article->title }}</a>
                    @if($article->category)
                        <span class="text-sm text-gray-500 dark:text-gray-400">— {{ $article->category->name }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection

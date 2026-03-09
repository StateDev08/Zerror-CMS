@extends('theme::layouts.app')

@section('title', $category->name . ' - ' . __('nav.wiki') . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('wiki.index') }}" class="hover:underline">{{ __('nav.wiki') }}</a> / {{ $category->name }}
    </nav>
    <h1 class="text-2xl font-bold mb-2">{{ $category->name }}</h1>
    @if($category->description)
        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $category->description }}</p>
    @endif
    @if($articles->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('wiki.no_articles') }}</p>
    @else
        <ul class="space-y-2">
            @foreach($articles as $article)
                <li><a href="{{ route('wiki.show', $article->slug) }}" class="hover:underline">{{ $article->title }}</a></li>
            @endforeach
        </ul>
    @endif
@endsection

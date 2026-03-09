@extends('theme::layouts.app')

@section('title', $article->title . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('wiki.index') }}" class="hover:underline">{{ __('nav.wiki') }}</a>
        @if($article->category)
            / <a href="{{ route('wiki.category', $article->category) }}" class="hover:underline">{{ $article->category->name }}</a>
        @endif
        / {{ $article->title }}
    </nav>
    <article class="prose dark:prose-invert max-w-none">
        <h1 class="text-2xl font-bold mb-4">{{ $article->title }}</h1>
        <div class="whitespace-pre-wrap">{{ $article->body }}</div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('wiki.updated') }} {{ $article->updated_at->format(__('general.date_format')) }}</p>
    </article>
@endsection

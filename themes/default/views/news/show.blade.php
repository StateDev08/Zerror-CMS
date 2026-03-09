@extends('theme::layouts.app')

@section('title', $post->title . ' - ' . config('clan.name'))

@section('content')
    <article class="card">
        <h1 class="page-title mb-2">{{ $post->title }}</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">{{ $post->created_at->format(__('general.date_format')) }}</p>
        <div class="prose article-body dark:prose-invert max-w-none">
            {!! $post->content !!}
        </div>
    </article>
@endsection

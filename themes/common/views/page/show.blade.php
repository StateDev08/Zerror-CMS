@extends('theme::layouts.app')

@section('title', (isset($page) && $page ? $page->title : $slug) . ' - ' . site_name())

@section('content')
    <article class="card">
        @if(isset($page) && $page)
            <h1 class="page-title mb-6">{{ $page->title }}</h1>
            <div class="prose article-body dark:prose-invert max-w-none">
            {!! html_content($page->content) !!}
            </div>
        @else
            <h1 class="page-title mb-4">{{ $slug }}</h1>
            <p class="text-neutral-500 dark:text-neutral-400">{{ __('general.no_content') }}</p>
        @endif
    </article>
@endsection

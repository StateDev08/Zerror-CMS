@extends('theme::layouts.app')

@section('title', __('nav.news') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-neutral-100 tracking-tight mb-6">{{ __('nav.news') }}</h1>
    @if($posts->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-6">{{ __('widgets.no_news') }}</p>
    @else
        <ul class="space-y-3">
            @foreach($posts as $post)
                <li class="rounded-xl border border-neutral-200/80 dark:border-neutral-700/80 bg-white dark:bg-neutral-900/60 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <a href="{{ route('news.show', $post->slug) }}" class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 hover:text-[var(--theme-primary)] transition-colors">{{ $post->title }}</a>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $post->created_at->format(__('general.date_format')) }}</p>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif
@endsection

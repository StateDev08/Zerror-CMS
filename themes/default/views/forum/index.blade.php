@extends('theme::layouts.app')

@section('title', __('nav.forum') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.forum') }}</h1>
    @if($categories->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">Noch keine Foren vorhanden.</p>
    @else
        @foreach($categories as $category)
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4">{{ $category->name }}</h2>
                <ul class="space-y-3">
                    @foreach($category->forums as $forum)
                        <li class="card flex items-center justify-between gap-4 group">
                            <div class="min-w-0">
                                <a href="{{ route('forum.show', $forum) }}" class="font-semibold text-neutral-900 dark:text-neutral-100 group-hover:text-[var(--theme-primary)] transition-colors">{{ $forum->name }}</a>
                                @if($forum->description)
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">{{ Str::limit($forum->description, 80) }}</p>
                                @endif
                            </div>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400 shrink-0">{{ $forum->threads()->count() }} {{ __('forum.threads') }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    @endif
@endsection

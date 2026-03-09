@extends('theme::layouts.app')

@section('title', $event->title . ' - ' . config('clan.name'))

@section('content')
    <article class="card">
        <h1 class="page-title mb-3">{{ $event->title }}</h1>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm mb-6">
            {{ $event->starts_at->format(__('general.date_format')) }}
            @if($event->ends_at) – {{ $event->ends_at->format(__('general.date_format')) }}@endif
            @if($event->location) · {{ $event->location }}@endif
        </p>
        @if($event->description)
            <div class="prose article-body dark:prose-invert max-w-none">
                {!! nl2br(e($event->description)) !!}
            </div>
        @endif
    </article>
@endsection

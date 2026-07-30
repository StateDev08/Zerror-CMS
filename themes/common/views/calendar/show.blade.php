@extends('theme::layouts.app')

@section('title', $event->title . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-5" style="color: var(--theme-muted);">
        <a href="{{ route('calendar.index', ['year' => $event->starts_at->year, 'month' => $event->starts_at->month]) }}" class="theme-link-primary hover:underline">{{ __('nav.calendar') }}</a>
        <span class="mx-1 opacity-60">/</span>
        <span style="color: var(--theme-text);">{{ $event->title }}</span>
    </nav>

    <article class="clan-frame panel-box rounded-xl p-5 md:p-6">
        <h1 class="font-display text-2xl font-semibold mb-3" style="color: var(--theme-text);">{{ $event->title }}</h1>
        <p class="text-sm mb-6" style="color: var(--theme-muted);">
            {{ $event->starts_at->timezone(config('app.timezone'))->format(__('general.date_format').' H:i') }}
            @if($event->ends_at) – {{ $event->ends_at->timezone(config('app.timezone'))->format(__('general.date_format').' H:i') }}@endif
            @if($event->location) · {{ $event->location }}@endif
            @if($event->type) · {{ __('calendar.type.'.$event->type) }}@endif
        </p>
        @if($event->description)
            <div class="text-sm leading-relaxed" style="color: var(--theme-text);">
                {!! \App\Support\HtmlContent::toHtml($event->description) !!}
            </div>
        @endif
    </article>
@endsection

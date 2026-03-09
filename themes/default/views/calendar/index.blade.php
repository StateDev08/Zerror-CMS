@extends('theme::layouts.app')

@section('title', __('nav.calendar') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.calendar') }}</h1>
    @if($events->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">{{ __('widgets.no_events') }}</p>
    @else
        <ul class="space-y-4">
            @foreach($events as $event)
                <li class="card group">
                    <a href="{{ route('calendar.show', $event->id) }}" class="block">
                        <span class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 group-hover:text-[var(--theme-primary)] transition-colors">{{ $event->title }}</span>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $event->starts_at->format(__('general.date_format')) }} @if($event->location)– {{ $event->location }}@endif</p>
                        @if($event->description)
                            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">{{ Str::limit(strip_tags($event->description), 150) }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @endif
@endsection

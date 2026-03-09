@extends('theme::layouts.app')

@section('title', __('nav.polls') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.polls') }}</h1>
    @if($polls->isEmpty())
        <p class="text-neutral-500 dark:text-neutral-400 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-8">{{ __('polls.none') }}</p>
    @else
        <ul class="space-y-4">
            @foreach($polls as $poll)
                <li class="card group">
                    <a href="{{ route('polls.show', $poll) }}" class="block">
                        <span class="font-semibold text-neutral-900 dark:text-neutral-100 group-hover:text-[var(--theme-primary)] transition-colors">{{ $poll->question }}</span>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                            {{ $poll->ends_at ? __('polls.ends_at', ['date' => $poll->ends_at->format(__('general.date_format'))]) : __('polls.no_deadline') }}
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection

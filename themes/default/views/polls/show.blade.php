@extends('theme::layouts.app')

@section('title', Str::limit($poll->question, 50) . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('polls.index') }}" class="hover:underline">{{ __('nav.polls') }}</a> / {{ Str::limit($poll->question, 40) }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ $poll->question }}</h1>
    @if($poll->ends_at && $poll->ends_at->isPast())
        <p class="text-gray-500 dark:text-gray-400 mb-4">{{ __('polls.ended') }}</p>
    @endif

    @if(session('voted'))
        <p class="text-green-600 dark:text-green-400 mb-4">{{ __('polls.thank_you') }}</p>
    @endif
    @if(session('error'))
        <p class="text-red-600 dark:text-red-400 mb-4">{{ session('error') }}</p>
    @endif

    @php
        $total = $poll->options->sum('votes_count');
    @endphp
    <div class="space-y-3 max-w-xl">
        @foreach($poll->options as $option)
            <div class="flex items-center gap-4">
                @if(!$hasVoted && (!$poll->ends_at || !$poll->ends_at->isPast()))
                    <form action="{{ route('polls.vote', $poll) }}" method="post" class="inline">
                        @csrf
                        <input type="hidden" name="option_id" value="{{ $option->id }}">
                        <button type="submit" class="text-left flex-1 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">
                            {{ $option->text }}
                        </button>
                    </form>
                @else
                    <span class="flex-1">{{ $option->text }}</span>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400 w-24 text-right">
                    {{ $option->votes_count }} {{ $total > 0 ? round($option->votes_count / $total * 100) . '%' : '' }}
                </span>
            </div>
        @endforeach
    </div>
@endsection

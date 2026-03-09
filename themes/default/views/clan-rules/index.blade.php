@extends('theme::layouts.app')

@section('title', __('nav.clan_rules') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.clan_rules') }}</h1>
    @if($rules->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('clan.rules_none') }}</p>
    @else
        <div class="space-y-6">
            @foreach($rules as $rule)
                <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $rule->title }}</h2>
                    <div class="prose dark:prose-invert max-w-none">{!! $rule->content !!}</div>
                </article>
            @endforeach
        </div>
    @endif
@endsection

@extends('theme::layouts.app')

@section('title', $job->title . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('jobs.index') }}" class="hover:underline">{{ __('nav.jobs') }}</a>
        @if($job->category)
            / <a href="{{ route('jobs.category', $job->category) }}" class="hover:underline">{{ $job->category->name }}</a>
        @endif
        / {{ $job->title }}
    </nav>
    <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-2">{{ $job->title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            @if($job->category) {{ $job->category->name }} · @endif
            {{ __('jobs.'.$job->employment_type) }}
            @if($job->location) · {{ $job->location }} @endif
            @if($job->expires_at) · {{ __('jobs.expires') }} {{ $job->expires_at->format(__('general.date_format')) }} @endif
        </p>
        <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap">{{ $job->description }}</div>
        @if($job->contact_email)
            <p class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <strong>{{ __('jobs.apply') }}</strong>
                <a href="mailto:{{ $job->contact_email }}" class="text-amber-600 dark:text-amber-400 hover:underline">{{ $job->contact_email }}</a>
            </p>
        @endif
    </article>
@endsection

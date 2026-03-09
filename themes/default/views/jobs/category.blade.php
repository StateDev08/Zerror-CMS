@extends('theme::layouts.app')

@section('title', $category->name . ' - ' . __('nav.jobs') . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('jobs.index') }}" class="hover:underline">{{ __('nav.jobs') }}</a> / {{ $category->name }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ $category->name }}</h1>
    @if($jobs->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">{{ __('jobs.no_jobs') }}</p>
    @else
        <ul class="space-y-4">
            @foreach($jobs as $job)
                <li class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <a href="{{ route('jobs.show', $job) }}" class="font-semibold hover:underline">{{ $job->title }}</a>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('jobs.'.$job->employment_type) }}@if($job->location) · {{ $job->location }}@endif
                        @if($job->expires_at) · {{ __('jobs.expires') }} {{ $job->expires_at->format(__('general.date_format')) }}@endif
                    </p>
                </li>
            @endforeach
        </ul>
        {{ $jobs->links() }}
    @endif
@endsection

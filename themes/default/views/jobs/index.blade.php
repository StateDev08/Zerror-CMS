@extends('theme::layouts.app')

@section('title', __('nav.jobs') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ __('nav.jobs') }}</h1>
    <div class="grid gap-8 lg:grid-cols-3">
        <aside class="lg:col-span-1">
            <h2 class="font-semibold mb-3">{{ __('jobs.categories') }}</h2>
            @if($categories->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('jobs.no_categories') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                        <li><a href="{{ route('jobs.category', $cat) }}" class="hover:underline">{{ $cat->name }} ({{ $cat->job_offers_count }})</a></li>
                    @endforeach
                </ul>
            @endif
        </aside>
        <div class="lg:col-span-2">
            <h2 class="font-semibold mb-3">{{ __('jobs.latest') }}</h2>
            @if($jobs->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">{{ __('jobs.no_jobs') }}</p>
            @else
                <ul class="space-y-4">
                    @foreach($jobs as $job)
                        <li class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <a href="{{ route('jobs.show', $job) }}" class="font-semibold hover:underline">{{ $job->title }}</a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                @if($job->category) {{ $job->category->name }} · @endif
                                {{ __('jobs.'.$job->employment_type) }}@if($job->location) · {{ $job->location }}@endif
                            </p>
                        </li>
                    @endforeach
                </ul>
                {{ $jobs->links() }}
            @endif
        </div>
    </div>
@endsection

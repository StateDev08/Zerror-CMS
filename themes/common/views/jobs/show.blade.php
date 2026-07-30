@extends('theme::layouts.app')

@section('title', $job->title . ' - ' . site_name())

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('jobs.index') }}" class="hover:underline">{{ __('nav.jobs') }}</a>
        @if($job->category)
            / <a href="{{ route('jobs.category', $job->category) }}" class="hover:underline">{{ $job->category->name }}</a>
        @endif
        / {{ $job->title }}
    </nav>
    <article class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-8">
        <h1 class="text-2xl font-bold mb-2">{{ $job->title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            @if($job->category) {{ $job->category->name }} · @endif
            {{ __('jobs.'.$job->employment_type) }}
            @if($job->location) · {{ $job->location }} @endif
            @if($job->expires_at) · {{ __('jobs.expires') }} {{ $job->expires_at->format(__('general.date_format')) }} @endif
        </p>
        <div class="prose dark:prose-invert max-w-none">{{ html_content($job->description) }}</div>
        @if($job->contact_email)
            <p class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <strong>{{ __('jobs.apply') }}</strong>
                <a href="mailto:{{ $job->contact_email }}" class="text-amber-600 dark:text-amber-400 hover:underline">{{ $job->contact_email }}</a>
            </p>
        @endif
    </article>

    <section class="max-w-lg">
        <h2 class="text-xl font-semibold mb-3">{{ __('jobs.application_title') }}</h2>
        @if(session('application_sent'))
            <div class="alert-success mb-6">{{ __('jobs.application_sent') }}</div>
        @endif
        <form action="{{ route('jobs.apply', $job) }}" method="POST" class="card space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('jobs.application_name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()?->name) }}" required class="form-input">
                @error('name')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('jobs.application_email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()?->email) }}" required class="form-input">
                @error('email')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('jobs.application_message') }}</label>
                <livewire:cms-rich-editor-field name="message" :value="old('message')" :compact="true" />
                @error('message')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary">{{ __('jobs.application_submit') }}</button>
        </form>
    </section>
@endsection

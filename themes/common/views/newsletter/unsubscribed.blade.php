@extends('theme::layouts.app')

@section('title', __('newsletter.unsubscribed') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.newsletter') }}</h1>
    <div class="alert-success mb-8">{{ __('newsletter.unsubscribed') }}</div>
    <p class="text-neutral-600 dark:text-neutral-400">
        <a href="{{ route('newsletter.index') }}" class="theme-link-primary hover:underline">{{ __('newsletter.subscribe_again') }}</a>
    </p>
@endsection

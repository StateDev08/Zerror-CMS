@extends('theme::layouts.app')

@section('title', __('apply.disabled_title', ['name' => site_name()]))

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.apply') }}</h1>
    <p class="mb-8 max-w-2xl" style="color: var(--theme-muted);">{{ __('apply.disabled_message') }}</p>

    <div class="clan-frame panel-box rounded-xl p-6 max-w-2xl text-center space-y-4">
        <h2 class="text-lg font-semibold" style="color: var(--theme-text);">{{ __('apply.disabled_heading') }}</h2>
        <p class="text-sm" style="color: var(--theme-muted);">{{ __('apply.disabled_message') }}</p>
        <a href="{{ route('home') }}" class="theme-bg-primary inline-flex px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('nav.home') }}</a>
    </div>
@endsection

@extends('theme::layouts.app')

@section('title', __('apply.disabled_title', ['name' => config('clan.name')]))

@section('content')
<div class="max-w-md mx-auto text-center">
    <h1 class="text-2xl font-semibold mb-4">{{ __('apply.disabled_heading') }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('apply.disabled_message') }}</p>
    <a href="{{ route('home') }}" class="inline-block px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-medium">{{ __('nav.home') }}</a>
</div>
@endsection

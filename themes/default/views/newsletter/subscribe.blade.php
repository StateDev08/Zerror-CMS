@extends('theme::layouts.app')

@section('title', __('nav.newsletter') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.newsletter') }}</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">{{ __('newsletter.hint') }}</p>
    @if(session('newsletter_status') === 'subscribed')
        <div class="alert-success mb-8">{{ __('newsletter.subscribed') }}</div>
    @elseif(session('newsletter_status') === 'already')
        <p class="text-neutral-600 dark:text-neutral-400 mb-8 rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/50 p-4">{{ __('newsletter.already_subscribed') }}</p>
    @endif
    <form action="{{ route('newsletter.subscribe') }}" method="post" class="card max-w-md space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('newsletter.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input">
            @error('email')
                <p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn-primary">{{ __('newsletter.submit') }}</button>
    </form>
@endsection

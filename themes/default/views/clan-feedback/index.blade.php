@extends('theme::layouts.app')

@section('title', __('nav.clan_feedback') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.clan_feedback') }}</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">{{ __('clan.feedback_intro') }}</p>
    @if(session('feedback_sent'))
        <div class="alert-success mb-8">{{ __('clan.feedback_sent') }}</div>
    @endif
    <form action="{{ route('clan-feedback.store') }}" method="POST" class="card space-y-5 max-w-lg">
        @csrf
        <div>
            <label for="author_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('clan.feedback_name') }}</label>
            <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}" required class="form-input">
            @error('author_name')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="author_email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('clan.feedback_email') }}</label>
            <input type="email" name="author_email" id="author_email" value="{{ old('author_email') }}" class="form-input">
            @error('author_email')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="message" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('clan.feedback_message') }}</label>
            <textarea name="message" id="message" rows="5" required class="form-input resize-y min-h-[120px]">{{ old('message') }}</textarea>
            @error('message')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">{{ __('clan.feedback_submit') }}</button>
    </form>
@endsection

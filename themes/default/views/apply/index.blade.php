@extends('theme::layouts.app')

@section('title', __('nav.apply') . ' - ' . config('clan.name'))

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.apply') }}</h1>
    <p class="text-neutral-600 dark:text-neutral-400 mb-8">{{ __('apply.intro', [], 'Sende uns deine Bewerbung über das Formular.') }}</p>
    @if(session('application_sent'))
        <div class="alert-success mb-8">{{ __('apply.sent') }}</div>
    @endif
    <form action="{{ route('apply.store') }}" method="POST" class="card space-y-5 max-w-lg">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('apply.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-input">
            @error('name')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('apply.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input">
            @error('email')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="message" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('apply.message') }}</label>
            <textarea name="message" id="message" rows="5" required class="form-input resize-y min-h-[120px]">{{ old('message') }}</textarea>
            @error('message')<p class="text-red-600 dark:text-red-400 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">{{ __('apply.submit') }}</button>
    </form>
@endsection

@extends('theme::layouts.app')

@section('title', config('clan.name') . ' - ' . __('auth.register'))

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-neutral-100 tracking-tight mb-6">{{ __('auth.register') }}</h1>
    @if(session('success'))
        <p class="mb-5 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-200 text-sm border border-emerald-200/80 dark:border-emerald-800/50">{{ session('success') }}</p>
    @endif
    <form action="{{ route('register') }}" method="POST" class="rounded-2xl bg-white dark:bg-neutral-900/80 border border-neutral-200/80 dark:border-neutral-700/80 p-6 md:p-8 space-y-5 shadow-lg">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('auth.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800/80 px-4 py-2.5 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[var(--theme-primary)]/30 focus:border-[var(--theme-primary)] transition-shadow">
            @error('name')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('auth.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800/80 px-4 py-2.5 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[var(--theme-primary)]/30 focus:border-[var(--theme-primary)] transition-shadow">
            @error('email')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('auth.password') }}</label>
            <input type="password" name="password" id="password" required autocomplete="new-password" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800/80 px-4 py-2.5 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[var(--theme-primary)]/30 focus:border-[var(--theme-primary)] transition-shadow">
            @error('password')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">{{ __('auth.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800/80 px-4 py-2.5 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[var(--theme-primary)]/30 focus:border-[var(--theme-primary)] transition-shadow">
        </div>
        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold transition-colors shadow-sm">{{ __('auth.register') }}</button>
    </form>
    <p class="mt-5 text-center text-sm text-neutral-600 dark:text-neutral-400">
        {{ __('auth.already_registered') }}
        <a href="{{ route('login') }}" class="text-[var(--theme-primary)] hover:underline font-medium">{{ __('auth.login') }}</a>
    </p>
</div>
@endsection

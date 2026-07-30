@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('auth.register'))

@section('content')
<div class="auth-shell">
    <h1 class="auth-shell__title font-display">{{ __('auth.register') }}</h1>
    @if(session('success'))
        <p class="auth-shell__flash auth-shell__flash--ok">{{ session('success') }}</p>
    @endif
    <form action="{{ route('register') }}" method="POST" class="auth-card">
        @csrf
        <div class="auth-card__field">
            <label for="name" class="auth-card__label">{{ __('auth.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="auth-card__input">
            @error('name')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-card__field">
            <label for="email" class="auth-card__label">{{ __('auth.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" class="auth-card__input">
            @error('email')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-card__field">
            <label for="password" class="auth-card__label">{{ __('auth.password') }}</label>
            <input type="password" name="password" id="password" required autocomplete="new-password" class="auth-card__input">
            @error('password')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-card__field">
            <label for="password_confirmation" class="auth-card__label">{{ __('auth.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="auth-card__input">
        </div>
        <button type="submit" class="auth-card__submit theme-bg-primary">{{ __('auth.register') }}</button>
    </form>
    <p class="auth-shell__footer">
        {{ __('auth.already_registered') }}
        <a href="{{ route('login') }}" class="auth-card__link">{{ __('auth.login') }}</a>
    </p>
</div>
@endsection

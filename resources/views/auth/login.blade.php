@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('auth.login'))

@section('content')
<div class="auth-shell">
    <h1 class="auth-shell__title font-display">{{ __('auth.login') }}</h1>
    @if(session('success'))
        <p class="auth-shell__flash auth-shell__flash--ok">{{ session('success') }}</p>
    @endif
    <form action="{{ route('login') }}" method="POST" class="auth-card">
        @csrf
        <div class="auth-card__field">
            <label for="email" class="auth-card__label">{{ __('auth.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="auth-card__input">
            @error('email')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-card__field">
            <div class="auth-card__label-row">
                <label for="password" class="auth-card__label">{{ __('auth.password') }}</label>
                <a href="{{ route('password.request') }}" class="auth-card__link">{{ __('auth.forgot_password') }}</a>
            </div>
            <input type="password" name="password" id="password" required autocomplete="current-password" class="auth-card__input">
            @error('password')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <label class="auth-card__check">
            <input type="checkbox" name="remember" id="remember" value="1">
            <span>{{ __('auth.remember_me') }}</span>
        </label>
        <button type="submit" class="auth-card__submit theme-bg-primary">{{ __('auth.login') }}</button>
    </form>
    <p class="auth-shell__footer">
        {{ __('auth.no_account') }}
        @if((bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN))
            <a href="{{ route('register') }}" class="auth-card__link">{{ __('auth.register') }}</a>
        @endif
    </p>
</div>
@endsection

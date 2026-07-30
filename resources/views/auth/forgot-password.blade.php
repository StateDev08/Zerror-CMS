@extends('theme::layouts.app')

@section('title', site_name() . ' - ' . __('auth.forgot_password'))

@section('content')
<div class="auth-shell">
    <h1 class="auth-shell__title font-display">{{ __('auth.forgot_password') }}</h1>
    @if(session('success'))
        <p class="auth-shell__flash auth-shell__flash--ok">{{ session('success') }}</p>
    @endif
    <form action="{{ route('password.email') }}" method="POST" class="auth-card">
        @csrf
        <div class="auth-card__field">
            <label for="email" class="auth-card__label">{{ __('auth.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="auth-card__input">
            @error('email')<p class="auth-card__error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="auth-card__submit theme-bg-primary">{{ __('auth.send_reset_link') }}</button>
    </form>
    <p class="auth-shell__footer">
        <a href="{{ route('login') }}" class="auth-card__link">{{ __('auth.login') }}</a>
    </p>
</div>
@endsection

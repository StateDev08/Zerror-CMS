@extends('usercp.layout')

@section('usercp_content')
    <h1 class="usercp-title">{{ __('usercp.password') }}</h1>
    <form action="{{ route('usercp.password.update') }}" method="POST" class="usercp-card space-y-4 max-w-xl">
        @csrf
        @method('PUT')
        <div>
            <label for="current_password" class="usercp-label">{{ __('usercp.current_password') }}</label>
            <input type="password" name="current_password" id="current_password" required class="usercp-input">
            @error('current_password')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="usercp-label">{{ __('usercp.new_password') }}</label>
            <input type="password" name="password" id="password" required class="usercp-input">
            @error('password')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="usercp-label">{{ __('usercp.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="usercp-input">
        </div>
        <button type="submit" class="usercp-btn-primary">{{ __('usercp.save') }}</button>
    </form>
@endsection

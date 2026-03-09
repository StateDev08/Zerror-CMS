@extends('usercp.layout')

@section('usercp_content')
    <h1 class="text-2xl font-semibold mb-4">{{ __('usercp.password') }}</h1>
    <form action="{{ route('usercp.password.update') }}" method="POST" class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 space-y-4 max-w-xl">
        @csrf
        @method('PUT')
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.current_password') }}</label>
            <input type="password" name="current_password" id="current_password" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('current_password')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.new_password') }}</label>
            <input type="password" name="password" id="password" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('password')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-medium">{{ __('usercp.save') }}</button>
    </form>
@endsection

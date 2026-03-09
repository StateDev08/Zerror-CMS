@extends('usercp.layout')

@section('usercp_content')
    <h1 class="text-2xl font-semibold mb-4">{{ __('usercp.profile') }}</h1>

    @if(session('success'))
        <p class="mb-4 p-3 rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</p>
    @endif

    <form action="{{ route('usercp.profile.update') }}" method="POST" enctype="multipart/form-data" class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 space-y-4 max-w-xl">
        @csrf
        @method('PUT')

        <div>
            <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.avatar') }}</label>
            @if($user->avatar_url)
                <div class="mb-2">
                    <img src="{{ $user->avatar_url }}" alt="" class="h-24 w-24 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                </div>
                <label class="flex items-center gap-2 mb-2">
                    <input type="hidden" name="avatar_remove" value="0">
                    <input type="checkbox" name="avatar_remove" value="1">
                    <span class="text-sm">{{ __('usercp.avatar_remove') }}</span>
                </label>
            @else
                <div class="mb-2 h-24 w-24 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-2xl font-semibold text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600" aria-hidden="true">{{ Str::limit(mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)), 2) }}</div>
            @endif
            <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-amber-600 file:text-white file:font-medium">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('usercp.avatar_upload_hint') }}</p>
            @error('avatar')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="job" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.job') }}</label>
            <input type="text" name="job" id="job" value="{{ old('job', $user->job) }}" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100" placeholder="{{ __('usercp.job_placeholder') }}">
            @error('job')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="biography" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.biography') }}</label>
            <textarea name="biography" id="biography" rows="4" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">{{ old('biography', $user->biography) }}</textarea>
            @error('biography')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="about_me" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.about_me') }}</label>
            <textarea name="about_me" id="about_me" rows="6" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">{{ old('about_me', $user->about_me) }}</textarea>
            @error('about_me')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.location') }}</label>
            <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('location')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.website') }}</label>
            <input type="url" name="website" id="website" value="{{ old('website', $user->website) }}" placeholder="{{ __('settings.placeholder_url') }}" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('website')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="discord_handle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('usercp.discord_handle') }}</label>
            <input type="text" name="discord_handle" id="discord_handle" value="{{ old('discord_handle', $user->discord_handle) }}" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100" placeholder="{{ __('usercp.discord_handle_placeholder') }}">
            @error('discord_handle')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-medium">{{ __('usercp.save') }}</button>
    </form>
@endsection

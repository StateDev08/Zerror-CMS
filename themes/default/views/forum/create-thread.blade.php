@extends('theme::layouts.app')

@section('title', __('forum.new_thread') . ' - ' . config('clan.name'))

@section('content')
    <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('forum.index') }}" class="hover:underline">{{ __('nav.forum') }}</a> /
        <a href="{{ route('forum.show', $forum) }}" class="hover:underline">{{ $forum->name }}</a> /
        {{ __('forum.new_thread') }}
    </nav>
    <h1 class="text-2xl font-bold mb-4">{{ __('forum.new_thread') }}</h1>
    <form action="{{ route('forum.thread.store', $forum) }}" method="POST" class="space-y-4 max-w-2xl">
        @csrf
        <div>
            <label for="title" class="block font-medium mb-1">{{ __('forum.thread_title') }}</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="block font-medium mb-1">{{ __('forum.first_post') }}</label>
            <textarea name="body" id="body" rows="8" required class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-2">{{ old('body') }}</textarea>
            @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded">{{ __('forum.create_thread') }}</button>
    </form>
@endsection

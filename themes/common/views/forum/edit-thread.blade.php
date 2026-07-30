@extends('theme::layouts.app')

@section('title', __('forum.edit') . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-4" style="color: var(--theme-muted);">
        <a href="{{ route('forum.index') }}" class="theme-link-primary hover:underline">{{ __('nav.forum') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('forum.show', $thread->forum) }}" class="theme-link-primary hover:underline">{{ $thread->forum->name }}</a>
        <span class="mx-1">/</span>
        <span style="color: var(--theme-text);">{{ __('forum.edit') }}</span>
    </nav>
    <h1 class="page-title mb-6">{{ __('forum.edit_thread') }}</h1>
    <form action="{{ route('forum.thread.update', $thread) }}" method="POST" class="clan-frame panel-box rounded-xl p-5 space-y-4 max-w-2xl">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="block font-semibold mb-1" style="color: var(--theme-text);">{{ __('forum.thread_title') }}</label>
            <input type="text" name="title" id="title" value="{{ old('title', $thread->title) }}" required class="w-full rounded-lg px-3 py-2" style="background: color-mix(in srgb, var(--theme-surface) 80%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);">
            @error('title')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="theme-bg-primary inline-flex px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('forum.save') }}</button>
            <a href="{{ route('forum.thread.show', $thread) }}" class="inline-flex px-4 py-2 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text);">{{ __('forum.cancel') }}</a>
        </div>
    </form>
@endsection

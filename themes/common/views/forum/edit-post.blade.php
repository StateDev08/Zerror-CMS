@extends('theme::layouts.app')

@section('title', __('forum.edit') . ' - ' . site_name())

@section('content')
    <nav class="text-sm mb-4" style="color: var(--theme-muted);">
        <a href="{{ route('forum.index') }}" class="theme-link-primary hover:underline">{{ __('nav.forum') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('forum.show', $post->thread->forum) }}" class="theme-link-primary hover:underline">{{ $post->thread->forum->name }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('forum.thread.show', $post->thread) }}" class="theme-link-primary hover:underline">{{ $post->thread->title }}</a>
        <span class="mx-1">/</span>
        <span style="color: var(--theme-text);">{{ __('forum.edit') }}</span>
    </nav>
    <h1 class="page-title mb-6">{{ __('forum.edit_post') }}</h1>
    <form action="{{ route('forum.post.update', $post) }}" method="POST" class="clan-frame panel-box rounded-xl p-5 space-y-4 max-w-2xl">
        @csrf
        @method('PUT')
        <div>
            <label class="block font-semibold mb-1" style="color: var(--theme-text);">{{ __('forum.reply') }}</label>
            <livewire:cms-rich-editor-field name="body" :value="old('body', $post->body)" :compact="true" />
            @error('body')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="theme-bg-primary inline-flex px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('forum.save') }}</button>
            <a href="{{ route('forum.thread.show', $post->thread) }}" class="inline-flex px-4 py-2 rounded-lg text-sm font-semibold" style="border: 1px solid color-mix(in srgb, var(--theme-primary) 40%, transparent); color: var(--theme-text);">{{ __('forum.cancel') }}</a>
        </div>
    </form>
@endsection

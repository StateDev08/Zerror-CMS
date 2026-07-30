@extends('theme::layouts.app')

@section('title', __('nav.apply') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-2">{{ __('nav.apply') }}</h1>
    <p class="mb-8 max-w-2xl" style="color: var(--theme-muted);">{{ __('apply.intro') }}</p>

    @if(session('application_sent'))
        <div class="clan-frame panel-box rounded-xl px-5 py-4 mb-6 max-w-2xl" style="border-color: color-mix(in srgb, #16a34a 45%, transparent);">
            <p class="text-sm" style="color: #bbf7d0;">{{ __('apply.sent') }}</p>
        </div>
    @endif

    <form action="{{ route('apply.store') }}" method="POST" class="clan-frame panel-box rounded-xl p-5 space-y-5 max-w-2xl">
        @csrf
        <div>
            <label for="name" class="block font-semibold mb-1" style="color: var(--theme-text);">{{ __('apply.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg px-3 py-2" style="background: color-mix(in srgb, var(--theme-surface) 80%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);">
            @error('name')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block font-semibold mb-1" style="color: var(--theme-text);">{{ __('apply.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full rounded-lg px-3 py-2" style="background: color-mix(in srgb, var(--theme-surface) 80%, #000); border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent); color: var(--theme-text);">
            @error('email')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block font-semibold mb-1" style="color: var(--theme-text);">{{ __('apply.message') }}</label>
            <livewire:cms-rich-editor-field name="message" :value="old('message')" :compact="true" />
            @error('message')<p class="text-sm mt-1" style="color:#fca5a5;">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="theme-bg-primary inline-flex px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider">{{ __('apply.submit') }}</button>
    </form>
@endsection

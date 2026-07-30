@extends('usercp.layout')

@section('usercp_content')
    <h1 class="usercp-title">{{ __('usercp.profile') }}</h1>

    <form action="{{ route('usercp.profile.update') }}" method="POST" enctype="multipart/form-data" class="usercp-card space-y-4 max-w-xl">
        @csrf
        @method('PUT')

        <div>
            <label for="avatar" class="usercp-label">{{ __('usercp.avatar') }}</label>
            @if($user->avatar_url)
                <div class="mb-2">
                    <img src="{{ $user->avatar_url }}" alt="" class="usercp-avatar">
                </div>
                <label class="flex items-center gap-2 mb-2">
                    <input type="hidden" name="avatar_remove" value="0">
                    <input type="checkbox" name="avatar_remove" value="1">
                    <span class="text-sm">{{ __('usercp.avatar_remove') }}</span>
                </label>
            @else
                <div class="usercp-avatar-fallback mb-2" aria-hidden="true">{{ Str::limit(mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)), 2) }}</div>
            @endif
            <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm usercp-muted">
            <p class="mt-1 text-xs usercp-muted">{{ __('usercp.avatar_upload_hint') }}</p>
            @error('avatar')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="name" class="usercp-label">{{ __('usercp.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="usercp-input">
            @error('name')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="usercp-label">{{ __('usercp.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="usercp-input">
            @error('email')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="job" class="usercp-label">{{ __('usercp.job') }}</label>
            <input type="text" name="job" id="job" value="{{ old('job', $user->job) }}" class="usercp-input" placeholder="{{ __('usercp.job_placeholder') }}">
            @error('job')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="usercp-label">{{ __('usercp.biography') }}</label>
            <livewire:cms-rich-editor-field name="biography" :value="old('biography', $user->biography)" :compact="true" />
            @error('biography')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="usercp-label">{{ __('usercp.about_me') }}</label>
            <livewire:cms-rich-editor-field name="about_me" :value="old('about_me', $user->about_me)" :compact="true" />
            @error('about_me')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="location" class="usercp-label">{{ __('usercp.location') }}</label>
            <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="usercp-input">
            @error('location')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="website" class="usercp-label">{{ __('usercp.website') }}</label>
            <input type="url" name="website" id="website" value="{{ old('website', $user->website) }}" placeholder="{{ __('settings.placeholder_url') }}" class="usercp-input">
            @error('website')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="discord_handle" class="usercp-label">{{ __('usercp.discord_handle') }}</label>
            <input type="text" name="discord_handle" id="discord_handle" value="{{ old('discord_handle', $user->discord_handle) }}" class="usercp-input" placeholder="{{ __('usercp.discord_handle_placeholder') }}">
            @error('discord_handle')<p class="mt-1 text-sm" style="color:#f87171">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="usercp-btn-primary">{{ __('usercp.save') }}</button>
    </form>
@endsection

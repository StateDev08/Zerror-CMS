@extends('usercp.layout')

@section('usercp_content')
    <h1 class="usercp-title">{{ __('usercp.overview') }}</h1>
    <div class="usercp-card">
        <div class="flex flex-wrap items-start gap-4">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="" class="usercp-avatar">
            @else
                <div class="usercp-avatar-fallback" aria-hidden="true">{{ Str::limit(mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)), 2) }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="usercp-muted m-0">{{ __('usercp.welcome') }}, <strong style="color: var(--ucp-text)">{{ $user->name }}</strong>.</p>
                <p class="usercp-muted mt-1 mb-0 text-sm">{{ $user->email }}</p>
                @if($user->job)
                    <p class="mt-2 mb-0 text-sm">{{ __('usercp.job') }}: {{ $user->job }}</p>
                @endif
                @if($user->about_me)
                    <p class="usercp-muted mt-2 mb-0 text-sm">{{ str()->limit(\App\Support\HtmlContent::plainText($user->about_me), 120) }}</p>
                @endif
            </div>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('usercp.profile') }}" class="usercp-btn-primary">{{ __('usercp.profile_edit') }}</a>
            @if(Route::has('profile.public'))
                <a href="{{ route('profile.public', ['user' => $user->id]) }}" class="usercp-btn">{{ __('usercp.view_public_profile') }}</a>
            @endif
            <a href="{{ route('usercp.password') }}" class="usercp-btn">{{ __('usercp.password') }}</a>
        </div>
    </div>
@endsection

@extends('usercp.layout')

@section('usercp_content')
    <h1 class="usercp-title">{{ __('usercp.discord') }}</h1>

    <div class="usercp-card space-y-6 max-w-xl">
        @if($user->discord_id)
            <div>
                <p class="usercp-label">{{ __('usercp.discord_status') }}</p>
                <p class="m-0" style="color:#86efac">{{ __('usercp.discord_linked') }}</p>
                <p class="mt-1 mb-0 text-sm usercp-muted">
                    ID: <code class="text-xs">{{ $user->discord_id }}</code>
                    @if($user->discord_handle)
                        · {{ $user->discord_handle }}
                    @endif
                </p>
            </div>
            <form action="{{ route('usercp.discord.unlink') }}" method="POST" onsubmit="return confirm(@json(__('usercp.discord_unlink_confirm')));">
                @csrf
                <button type="submit" class="usercp-btn-danger">{{ __('usercp.discord_unlink') }}</button>
            </form>
        @else
            <p class="usercp-muted m-0">{{ __('usercp.discord_not_linked') }}</p>
        @endif

        <div class="pt-6 space-y-4" style="border-top:1px solid var(--ucp-border)">
            <p class="text-sm usercp-muted m-0">{{ __('usercp.discord_link_help') }}</p>

            @if($user->discord_link_token && $user->discord_link_token_expires_at && $user->discord_link_token_expires_at->isFuture())
                <div class="p-4 rounded-lg space-y-2" style="background:color-mix(in srgb, var(--ucp-primary) 14%, transparent);border:1px solid color-mix(in srgb, var(--ucp-primary) 40%, transparent)">
                    <p class="text-sm font-medium m-0">{{ __('usercp.discord_your_token') }}</p>
                    <code class="block break-all text-sm select-all">{{ $user->discord_link_token }}</code>
                    <p class="text-sm usercp-muted m-0">{{ __('usercp.discord_token_instruction') }}</p>
                    <p class="text-xs usercp-muted m-0">{{ __('usercp.discord_token_expires', ['time' => $user->discord_link_token_expires_at->timezone(config('app.timezone'))->format('H:i')]) }}</p>
                </div>
            @endif

            <form action="{{ route('usercp.discord.generate') }}" method="POST">
                @csrf
                <button type="submit" class="usercp-btn-primary">{{ __('usercp.discord_generate_token') }}</button>
            </form>
        </div>
    </div>
@endsection

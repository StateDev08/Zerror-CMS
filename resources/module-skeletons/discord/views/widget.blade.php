@php
    $invite = $invite ?? null;
    $data = $data ?? ['ok'=>false,'name'=>null,'presence_count'=>0,'members'=>[],'channels'=>[],'error'=>null];
@endphp
<div class="cms-widget cms-widget--discord">
    <div class="cms-discord__top">
        <span class="cms-discord__label">
            <span class="cms-discord__logo" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.8 19.8 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.3 18.3 0 0 0-5.487 0 12.6 12.6 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.7 19.7 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.08.08 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14 14 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.1 13.1 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.3 12.3 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.8 19.8 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
            </span>
            {{ __('widgets.discord_title') }}
        </span>
        @if($showOnline && ($data['ok'] ?? false))
            <p class="cms-discord__online">
                <span class="cms-dot cms-dot--online" aria-hidden="true"></span>
                {{ __('widgets.discord_online_count', ['count' => (int)($data['presence_count'] ?? 0)]) }}
            </p>
        @endif
    </div>

    <h3 class="cms-discord__name">{{ $displayName }}</h3>
    @if($tagline !== '')
        <p class="cms-widget__tagline">{{ $tagline }}</p>
    @endif

    @if(!($data['ok'] ?? false))
        <div class="cms-discord__setup">
            <p class="cms-widget__hint" style="margin:0">
                @if(($data['error'] ?? '') === 'widget_disabled')
                    {{ __('widgets.discord_error_widget_disabled') }}
                @elseif(($data['error'] ?? '') === 'missing_guild')
                    {{ __('widgets.discord_error_missing') }}
                @else
                    {{ __('widgets.discord_error_generic') }}
                @endif
            </p>
        </div>
    @endif

    @if($showChannels && !empty($data['channels']))
        <div class="cms-widget__section">
            <p class="cms-widget__section-title">{{ __('widgets.discord_voice') }}</p>
            <ul class="cms-discord-channels" style="list-style:none;margin:0;padding:0">
                @foreach(array_slice($data['channels'], 0, 6) as $ch)
                    <li>
                        <span class="cms-discord-channels__diamond" aria-hidden="true"></span>
                        <span>{{ $ch['name'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($showMembers && !empty($data['members']))
        <div class="cms-widget__section">
            <p class="cms-widget__section-title">{{ __('widgets.discord_members') }}</p>
            <ul class="cms-discord-members" style="list-style:none;margin:0;padding:0">
                @foreach(array_slice($data['members'], 0, $memberLimit) as $m)
                    <li>
                        @if(!empty($m['avatar']))
                            <img class="cms-discord-members__avatar" src="{{ $m['avatar'] }}" alt="" width="24" height="24" loading="lazy">
                        @else
                            <span class="cms-discord-members__avatar cms-discord-members__avatar--fallback" aria-hidden="true">{{ mb_substr($m['username'] ?? '?', 0, 1) }}</span>
                        @endif
                        <span>{{ $m['username'] ?? 'User' }}</span>
                        <span class="cms-dot cms-dot--{{ ($m['status'] ?? 'online') === 'idle' ? 'idle' : (($m['status'] ?? '') === 'dnd' ? 'dnd' : 'online') }}" aria-hidden="true"></span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($invite)
        <a class="cms-widget__cta cms-widget__cta--discord" href="{{ $invite }}" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M20.317 4.37a19.8 19.8 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.3 18.3 0 0 0-5.487 0 12.6 12.6 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.7 19.7 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.08.08 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14 14 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.1 13.1 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.3 12.3 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.8 19.8 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>
            {{ $buttonText }}
        </a>
    @endif
</div>
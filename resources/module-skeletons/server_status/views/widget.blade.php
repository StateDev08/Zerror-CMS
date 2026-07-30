<div class="cms-widget cms-widget--servers">
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
        @if($showPageLink && \Illuminate\Support\Facades\Route::has('servers.index'))
            <a href="{{ route('servers.index') }}" class="cms-widget__more">{{ __('widgets.view_all') }}</a>
        @endif
    </header>
    @if(empty($servers))
        <p class="cms-widget__hint">{{ __('widgets.server_status_empty') }}</p>
    @else
        <ul class="cms-server-list" style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:0.55rem">
            @foreach($servers as $server)
                @php
                    $st = $server['status'] ?? [];
                    $online = (bool) ($st['online'] ?? false);
                    $connect = trim((string) ($server['connect'] ?? ''));
                    $map = trim((string) ($st['map'] ?? ''));
                    $motd = trim(strip_tags((string) ($st['motd'] ?? '')));
                    $banner = trim((string) ($server['banner'] ?? ''));
                @endphp
                <li class="cms-server-card" style="border:1px solid color-mix(in srgb, var(--theme-primary) 25%, transparent);border-radius:0.55rem;padding:0.55rem 0.65rem;background:color-mix(in srgb,#000 18%,transparent)">
                    <div style="display:flex;justify-content:space-between;gap:0.5rem;align-items:center">
                        <strong style="font-size:0.9rem">{{ $server['label'] }}</strong>
                        <span class="cms-widget__meta">
                            <span class="cms-dot {{ $online ? 'cms-dot--online' : 'cms-dot--offline' }}" aria-hidden="true"></span>
                            {{ $online ? __('widgets.server_status_online') : __('widgets.server_status_offline') }}
                        </span>
                    </div>
                    <p class="cms-widget__meta" style="margin-top:0.35rem">
                        {{ $server['game'] ?? '' }}
                        @if($connect !== '')
                            · {{ $connect }}
                        @endif
                    </p>
                    @if($online)
                        <p class="cms-widget__meta">
                            @if($showPlayers)
                                {{ __('widgets.server_status_online_players') }}:
                                {{ (int) ($st['players'] ?? 0) }}
                                @if(isset($st['max_players']))
                                    /{{ (int) $st['max_players'] }}
                                @endif
                            @endif
                            @if($showMap && $map !== '')
                                · {{ __('widgets.server_status_map') }}: {{ $map }}
                            @endif
                            @if($showPing && isset($st['ping']))
                                · {{ __('widgets.server_status_ping') }}: {{ (int) $st['ping'] }}ms
                            @endif
                        </p>
                        @if($showMotd && $motd !== '')
                            <p class="cms-widget__hint" style="margin-top:0.35rem">{{ \Illuminate\Support\Str::limit($motd, 80) }}</p>
                        @endif
                    @endif
                    @if($banner !== '')
                        <img src="{{ $banner }}" alt="" loading="lazy" style="margin-top:0.45rem;width:100%;height:auto;border-radius:0.4rem;display:block">
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>

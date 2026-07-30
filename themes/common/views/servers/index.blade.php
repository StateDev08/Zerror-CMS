@extends('theme::layouts.app')

@section('title', $pageTitle . ' - ' . site_name())

@section('content')
<section class="servers-page">
    <header class="mb-6">
        <h1 class="font-display text-3xl md:text-4xl m-0" style="color: var(--theme-text)">{{ $pageTitle }}</h1>
        @if(!empty($pageIntro))
            <p class="mt-2 text-sm md:text-base" style="color: var(--theme-muted)">{{ $pageIntro }}</p>
        @endif
    </header>

    @if(empty($servers))
        <div class="panel-box p-6">
            <p class="m-0 text-sm" style="color: var(--theme-muted)">{{ __('servers.empty') }}</p>
        </div>
    @else
        <div class="servers-page__grid">
            @foreach($servers as $entry)
                @php
                    $status = $entry['status'];
                    $online = (bool) ($status['online'] ?? false);
                    $connect = $entry['connect'] ?? ($entry['host'].':'.$entry['port']);
                    $modType = $entry['mod_type'] ?? 'vanilla';
                @endphp
                <article class="servers-page__card panel-box overflow-hidden">
                    @if(!empty($entry['banner']))
                        <div class="servers-page__banner">
                            <img src="{{ $entry['banner'] }}" alt="{{ $entry['label'] }}" loading="lazy">
                        </div>
                    @endif

                    <div class="servers-page__body">
                        <div class="servers-page__top">
                            <div class="min-w-0">
                                <p class="servers-page__game">{{ $entry['game'] }}</p>
                                <h2 class="servers-page__name">{{ $entry['label'] }}</h2>
                            </div>
                            <span class="servers-page__badge {{ $online ? 'is-online' : 'is-offline' }}">
                                <span class="cms-dot {{ $online ? 'cms-dot--online' : 'cms-dot--dnd' }}"></span>
                                {{ $online ? __('widgets.server_online') : __('widgets.server_offline') }}
                            </span>
                        </div>

                        <dl class="servers-page__stats">
                            <div>
                                <dt>{{ __('servers.host') }}</dt>
                                <dd><code>{{ $connect }}</code></dd>
                            </div>
                            <div>
                                <dt>{{ __('servers.mod_type') }}</dt>
                                <dd>
                                    <span class="servers-page__mod servers-page__mod--{{ $modType }}">
                                        {{ $modType === 'modded' ? __('servers.mod_modded') : __('servers.mod_vanilla') }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt>{{ __('servers.query_port') }}</dt>
                                <dd><code>{{ $entry['query_port'] }}</code></dd>
                            </div>
                            @if(($status['players'] ?? null) !== null)
                                <div>
                                    <dt>{{ __('widgets.server_status_players') }}</dt>
                                    <dd>{{ $status['players'] }}@if(($status['max_players'] ?? null) !== null) / {{ $status['max_players'] }}@endif</dd>
                                </div>
                            @endif
                            @if(($status['latency_ms'] ?? null) !== null)
                                <div>
                                    <dt>{{ __('widgets.server_status_ping') }}</dt>
                                    <dd>{{ $status['latency_ms'] }} ms</dd>
                                </div>
                            @endif
                            @if(!empty($status['version']))
                                <div class="servers-page__full">
                                    <dt>{{ __('widgets.server_status_version') }}</dt>
                                    <dd>{{ $status['version'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($status['map']))
                                <div class="servers-page__full">
                                    <dt>{{ __('widgets.server_status_map') }}</dt>
                                    <dd>{{ $status['map'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($status['motd']))
                                <div class="servers-page__full">
                                    <dt>{{ __('widgets.server_status_motd') }}</dt>
                                    <dd>{{ $status['motd'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($status['players_sample']))
                                <div class="servers-page__full">
                                    <dt>{{ __('widgets.server_status_online_players') }}</dt>
                                    <dd>{{ implode(', ', $status['players_sample']) }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="servers-page__actions">
                            <button
                                type="button"
                                class="servers-page__copy theme-bg-primary"
                                data-copy="{{ $connect }}"
                                data-label-idle="{{ __('servers.copy_ip') }}"
                                data-label-done="{{ __('servers.copy_ip_done') }}"
                            >
                                {{ __('servers.copy_ip') }}
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

<style>
    .servers-page__grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 900px) {
        .servers-page__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .servers-page__banner {
        width: 100%;
        overflow: hidden;
        background: color-mix(in srgb, var(--theme-primary) 10%, transparent);
    }
    .servers-page__banner img {
        display: block;
        width: 100%;
        height: auto;
        max-height: none;
        object-fit: contain;
        object-position: center;
    }
    .servers-page__body { padding: 1rem 1.1rem 1.15rem; }
    .servers-page__top {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: flex-start;
        margin-bottom: 0.85rem;
    }
    .servers-page__game {
        margin: 0;
        font-size: 0.72rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--theme-primary);
        font-weight: 700;
    }
    .servers-page__name {
        margin: 0.2rem 0 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--theme-text);
    }
    .servers-page__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.3rem 0.55rem;
        border-radius: 999px;
        white-space: nowrap;
    }
    .servers-page__badge.is-online { background: rgba(34,197,94,.15); color: #4ade80; }
    .servers-page__badge.is-offline { background: rgba(239,68,68,.15); color: #f87171; }
    .servers-page__mod {
        display: inline-flex;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.15rem 0.45rem;
        border-radius: 0.4rem;
    }
    .servers-page__mod--vanilla {
        background: color-mix(in srgb, var(--theme-primary) 14%, transparent);
        color: var(--theme-primary);
    }
    .servers-page__mod--modded {
        background: color-mix(in srgb, var(--theme-accent, #6b8cff) 18%, transparent);
        color: var(--theme-accent, #6b8cff);
    }
    .servers-page__stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.65rem 0.85rem;
        margin: 0;
    }
    .servers-page__stats dt {
        font-size: 0.7rem;
        color: var(--theme-muted);
        margin-bottom: 0.15rem;
    }
    .servers-page__stats dd {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--theme-text);
        word-break: break-word;
    }
    .servers-page__stats code {
        font-size: 0.85rem;
        color: var(--theme-primary);
    }
    .servers-page__full { grid-column: 1 / -1; }
    .servers-page__actions {
        margin-top: 1rem;
        padding-top: 0.85rem;
        border-top: 1px solid color-mix(in srgb, var(--theme-primary) 16%, transparent);
    }
    .servers-page__copy {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border: 0;
        border-radius: 0.7rem;
        padding: 0.7rem 1rem;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        cursor: pointer;
        color: #041018;
    }
    .servers-page__copy.is-copied {
        filter: brightness(1.08);
    }
</style>
<script>
(() => {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const value = btn.getAttribute('data-copy') || '';
            const idle = btn.getAttribute('data-label-idle') || 'Copy';
            const done = btn.getAttribute('data-label-done') || 'Copied';
            try {
                await navigator.clipboard.writeText(value);
            } catch (e) {
                const ta = document.createElement('textarea');
                ta.value = value;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
            }
            btn.textContent = done;
            btn.classList.add('is-copied');
            setTimeout(() => {
                btn.textContent = idle;
                btn.classList.remove('is-copied');
            }, 1600);
        });
    });
})();
</script>
@endsection

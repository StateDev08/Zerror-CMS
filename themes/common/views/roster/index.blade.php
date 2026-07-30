@extends('theme::layouts.app')

@section('title', __('nav.roster') . ' - ' . site_name())

@section('content')
    <h1 class="page-title mb-8">{{ __('nav.roster') }}</h1>

    @if($entries->isEmpty())
        <p class="rounded-2xl p-8 text-center" style="color: var(--theme-muted); background: color-mix(in srgb, var(--theme-surface) 80%, transparent);">{{ __('clan.no_members') }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($entries as $entry)
                <article class="clan-frame panel-box rounded-xl p-5 flex flex-col gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        @if(! empty($entry['avatar']))
                            <img src="{{ $entry['avatar'] }}" alt="" class="w-14 h-14 rounded-xl object-cover shrink-0" style="box-shadow: 0 0 0 1px color-mix(in srgb, var(--theme-primary) 35%, transparent);">
                        @else
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center font-semibold text-xl shrink-0" style="background: color-mix(in srgb, var(--theme-surface) 70%, #000); color: var(--theme-muted); box-shadow: 0 0 0 1px color-mix(in srgb, var(--theme-primary) 25%, transparent);">
                                {{ mb_substr($entry['name'], 0, 1) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="font-display font-semibold text-lg truncate" style="color: var(--theme-text);">{{ $entry['name'] }}</h2>
                            @if(! empty($entry['position']))
                                <p class="text-sm truncate" style="color: var(--theme-muted);">{{ $entry['position'] }}</p>
                            @endif
                        </div>
                    </div>

                    <dl class="grid gap-2 text-sm">
                        <div class="flex items-baseline justify-between gap-3">
                            <dt style="color: var(--theme-muted);">{{ __('clan.rank') }}</dt>
                            <dd class="font-semibold text-right" style="color: {{ $entry['rank_color'] ?: 'var(--theme-primary)' }};">
                                {{ $entry['rank'] ?: __('clan.rank_none') }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-3">
                            <dt style="color: var(--theme-muted);">{{ __('clan.registered_since') }}</dt>
                            <dd class="text-right" style="color: var(--theme-text);">
                                @if(! empty($entry['registered_at']))
                                    {{ $entry['registered_at']->timezone(config('app.timezone'))->format(site_date_format()) }}
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>
    @endif
@endsection

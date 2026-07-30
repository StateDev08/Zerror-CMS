<x-filament-panels::page>
    <div class="zc-page">
        @if($pluginsTableMissing ?? false)
            <div class="zc-note">{{ __('zerrocms.plugins.table_missing') }}</div>
        @endif

        <div class="zc-help">
            <p>{{ __('zerrocms.plugins.intro') }}</p>
            @include('filament.partials.zc-glossary')
            <p class="zc-help-tip">
                {{ __('zerrocms.plugins.help_page') }}
                <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
            </p>
            @include('filament.partials.zc-context-links')
        </div>

        @if(empty($plugins))
            <div class="zc-empty">
                <h3 class="zc-empty__title">{{ __('zerrocms.plugins.none_title') }}</h3>
                <p class="zc-empty__body">{{ __('zerrocms.plugins.none_body') }}</p>
                <p class="zc-empty__hint">
                    <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
                </p>
            </div>
        @else
            @php $activeCount = collect($plugins)->where('enabled', true)->count(); @endphp
            <div class="zc-toolbar">
                <div class="zc-stats">
                    <span class="zc-badge zc-badge--ok">{{ __('zerrocms.plugins.stats_active', ['count' => $activeCount]) }}</span>
                    <span>{{ __('zerrocms.plugins.stats_total', ['count' => count($plugins)]) }}</span>
                </div>
                <x-filament::button wire:click="savePluginOrders" size="sm" color="gray" outlined>{{ __('zerrocms.plugins.save_order') }}</x-filament::button>
            </div>
            <div class="zc-grid">
                @foreach($plugins as $key => $plugin)
                    @php
                        $pluginName = $plugin['name'] ?? $key;
                        $label = $plugin['manifest']['label'] ?? $pluginName;
                        $enabled = (bool) ($plugin['enabled'] ?? false);
                        $hasConfig = app(\App\Support\PluginManager::class)->getConfigSchema($pluginName) !== [];
                        $safePlugin = str_replace(["\\", "'"], ["\\\\", "\\'"], $pluginName);
                    @endphp
                    <article class="zc-card">
                        <div class="zc-card__head">
                            <h3 class="zc-card__title">{{ $label }}</h3>
                            @if(!empty($plugin['manifest']['version']))<span class="zc-card__meta">v{{ $plugin['manifest']['version'] }}</span>@endif
                            <span class="zc-badge {{ $enabled ? 'zc-badge--ok' : 'zc-badge--off' }}">{{ $enabled ? __('zerrocms.status.active') : __('zerrocms.status.inactive') }}</span>
                        </div>
                        @if(!empty($plugin['manifest']['description']))
                            <p class="zc-card__desc">{{ $plugin['manifest']['description'] }}</p>
                        @endif
                        <p class="zc-card__meta">{{ $pluginName }}</p>
                        <div class="zc-card__actions">
                            <div class="zc-field">
                                <label>{{ __('zerrocms.plugins.order') }}</label>
                                <input type="number" class="zc-input zc-narrow" wire:model="pluginOrders.{{ $key }}" min="0">
                            </div>
                            <div style="margin-left:auto;display:flex;gap:0.5rem;flex-wrap:wrap">
                                @if($hasConfig)
                                    <x-filament::button wire:click="openPluginConfig('{{ $safePlugin }}')" color="gray" size="sm" outlined>{{ __('zerrocms.plugins.configure') }}</x-filament::button>
                                @endif
                                <x-filament::button wire:click="togglePlugin('{{ $safePlugin }}')" :color="$enabled ? 'danger' : 'success'" size="sm">{{ $enabled ? __('zerrocms.plugins.deactivate') : __('zerrocms.plugins.activate') }}</x-filament::button>
                                <x-filament::button wire:click="uninstallPlugin('{{ $safePlugin }}')" wire:confirm="{{ __('zerrocms.packages.delete_confirm', ['name' => $label]) }}" color="danger" size="sm" outlined>{{ __('zerrocms.packages.delete') }}</x-filament::button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    @if($configuringPlugin ?? null)
        <div class="zc-modal" role="dialog" aria-modal="true">
            <div class="zc-modal__backdrop" wire:click="closePluginConfig"></div>
            <div class="zc-modal__panel">
                <div class="zc-modal__header">
                    <h2>{{ __('zerrocms.plugins.configure_title', ['name' => $configuringPlugin]) }}</h2>
                    <button type="button" class="zc-modal__close" wire:click="closePluginConfig">×</button>
                </div>
                <form wire:submit="savePluginConfig" class="zc-modal__body">
                    @include('filament.partials.config-fields', ['schema' => $pluginConfigSchema, 'formModel' => 'pluginConfigForm'])
                    <div class="zc-modal__footer">
                        <x-filament::button type="button" color="gray" wire:click="closePluginConfig">{{ __('zerrocms.plugins.cancel') }}</x-filament::button>
                        <x-filament::button type="submit">{{ __('zerrocms.plugins.save') }}</x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>

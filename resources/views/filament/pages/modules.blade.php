<x-filament-panels::page>
    <div class="zc-page">
        @if($modulesTableMissing ?? false)
            <div class="zc-note">{{ __('zerrocms.modules.table_missing') }}</div>
        @endif

        <div class="zc-help">
            <p>{{ __('zerrocms.modules.intro') }}</p>
            @include('filament.partials.zc-glossary')
            <p class="zc-help-tip">
                {{ __('zerrocms.modules.help_page') }}
                <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
            </p>
            @include('filament.partials.zc-context-links')
        </div>

        @if(empty($modules))
            <div class="zc-empty">
                <h3 class="zc-empty__title">{{ __('zerrocms.modules.none_title') }}</h3>
                <p class="zc-empty__body">{{ __('zerrocms.modules.none_body') }}</p>
                <p class="zc-empty__hint">
                    <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
                </p>
            </div>
        @else
            @php $activeCount = collect($modules)->where('enabled', true)->count(); @endphp
            <div class="zc-stats">
                <span class="zc-badge zc-badge--ok">{{ __('zerrocms.modules.stats_active', ['count' => $activeCount]) }}</span>
                <span>{{ __('zerrocms.modules.stats_total', ['count' => count($modules)]) }}</span>
            </div>
            <div class="zc-grid">
                @foreach($modules as $key => $module)
                    @php
                        $moduleName = $module['name'] ?? $key;
                        $enabled = (bool) ($module['enabled'] ?? false);
                        $hasConfig = app(\App\Support\ModuleManager::class)->getConfigSchema($key) !== [];
                        $safeKey = str_replace(["\\", "'"], ["\\\\", "\\'"], $key);
                    @endphp
                    <article class="zc-card">
                        <div class="zc-card__head">
                            <h3 class="zc-card__title">{{ $moduleName }}</h3>
                            @if(!empty($module['version']))<span class="zc-card__meta">v{{ $module['version'] }}</span>@endif
                            <span class="zc-badge {{ $enabled ? 'zc-badge--ok' : 'zc-badge--off' }}">{{ $enabled ? __('zerrocms.status.active') : __('zerrocms.status.inactive') }}</span>
                        </div>
                        @if(!empty($module['description']))
                            <p class="zc-card__desc">{{ $module['description'] }}</p>
                        @endif
                        <p class="zc-card__meta">{{ $key }}</p>
                        <div class="zc-card__actions">
                            @if($hasConfig)
                                <x-filament::button wire:click="openModuleConfig('{{ $safeKey }}')" color="gray" size="sm" outlined>{{ __('zerrocms.modules.configure') }}</x-filament::button>
                            @endif
                            <x-filament::button wire:click="toggleModule('{{ $safeKey }}')" :color="$enabled ? 'danger' : 'success'" size="sm">{{ $enabled ? __('zerrocms.modules.deactivate') : __('zerrocms.modules.activate') }}</x-filament::button>
                            <x-filament::button wire:click="uninstallModule('{{ $safeKey }}')" wire:confirm="{{ __('zerrocms.packages.delete_confirm', ['name' => $moduleName]) }}" color="danger" size="sm" outlined>{{ __('zerrocms.packages.delete') }}</x-filament::button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    @if($configuringModule ?? null)
        <div class="zc-modal" role="dialog" aria-modal="true">
            <div class="zc-modal__backdrop" wire:click="closeModuleConfig"></div>
            <div class="zc-modal__panel">
                <div class="zc-modal__header">
                    <h2>{{ __('zerrocms.modules.configure_title', ['name' => $configuringModule]) }}</h2>
                    <button type="button" class="zc-modal__close" wire:click="closeModuleConfig">×</button>
                </div>
                <form wire:submit="saveModuleConfig" class="zc-modal__body">
                    @include('filament.partials.config-fields', [
                        'schema' => $moduleConfigSchema,
                        'formModel' => 'moduleConfigForm',
                        'uploadMaxFileMb' => $uploadMaxFileMb ?? \App\Support\UploadLimits::fileMb(),
                    ])
                    <div class="zc-modal__footer">
                        <x-filament::button type="button" color="gray" wire:click="closeModuleConfig">{{ __('zerrocms.modules.cancel') }}</x-filament::button>
                        <x-filament::button type="submit">{{ __('zerrocms.modules.save') }}</x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>

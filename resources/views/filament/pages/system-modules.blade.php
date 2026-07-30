<x-filament-panels::page>
    <div class="zc-page">
        @if($tableMissing ?? false)
            <div class="zc-note">{{ __('zerrocms.system_modules.table_missing') }}</div>
        @endif

        <div class="zc-help">
            <p>{{ __('zerrocms.system_modules.intro') }}</p>
            <p class="zc-help-tip">
                {{ __('zerrocms.system_modules.help_page') }}
                <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
            </p>
        </div>

        @if(empty($systemModules))
            <div class="zc-empty">
                <h3 class="zc-empty__title">{{ __('zerrocms.system_modules.none_title') }}</h3>
                <p class="zc-empty__body">{{ __('zerrocms.system_modules.none_body') }}</p>
                <p class="zc-empty__hint">
                    <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
                </p>
            </div>
        @else
            @php $activeCount = collect($systemModules)->where('enabled', true)->count(); @endphp
            <div class="zc-stats">
                <span class="zc-badge zc-badge--ok">{{ __('zerrocms.system_modules.stats_active', ['count' => $activeCount]) }}</span>
                <span>{{ __('zerrocms.system_modules.stats_total', ['count' => count($systemModules)]) }}</span>
            </div>
            <div class="zc-grid">
                @foreach($systemModules as $key => $item)
                    @php
                        $label = $item['name'] ?? $key;
                        $enabled = (bool) ($item['enabled'] ?? false);
                        $safeKey = str_replace(["\\", "'"], ["\\\\", "\\'"], $key);
                    @endphp
                    <article class="zc-card">
                        <div class="zc-card__head">
                            <h3 class="zc-card__title">{{ $label }}</h3>
                            @if(!empty($item['version']))
                                <span class="zc-card__meta">v{{ $item['version'] }}</span>
                            @endif
                            <span class="zc-badge {{ $enabled ? 'zc-badge--ok' : 'zc-badge--off' }}">
                                {{ $enabled ? __('zerrocms.status.active') : __('zerrocms.status.inactive') }}
                            </span>
                        </div>
                        @if(!empty($item['description']))
                            <p class="zc-card__desc">{{ $item['description'] }}</p>
                        @endif
                        <p class="zc-card__meta">{{ $key }}</p>
                        <div class="zc-card__actions">
                            @if($enabled)
                                @foreach(($item['admin_actions'] ?? []) as $adminAction)
                                    @php
                                        $actionId = str_replace(["\\", "'"], ["\\\\", "\\'"], (string) ($adminAction['id'] ?? ''));
                                        $actionExtra = [];
                                        if (! empty($adminAction['confirm'])) {
                                            $actionExtra['wire:confirm'] = (string) $adminAction['confirm'];
                                        }
                                    @endphp
                                    <x-filament::button
                                        wire:click="runSystemModuleAdminAction('{{ $safeKey }}', '{{ $actionId }}')"
                                        wire:loading.attr="disabled"
                                        :color="$adminAction['color'] ?? 'primary'"
                                        size="sm"
                                        :outlined="(bool) ($adminAction['outlined'] ?? false)"
                                        :attributes="new \Illuminate\View\ComponentAttributeBag($actionExtra)"
                                    >
                                        {{ $adminAction['label'] ?? $adminAction['id'] }}
                                    </x-filament::button>
                                @endforeach
                            @endif
                            <x-filament::button
                                wire:click="toggleSystemModule('{{ $safeKey }}')"
                                :color="$enabled ? 'danger' : 'success'"
                                size="sm"
                            >
                                {{ $enabled ? __('zerrocms.system_modules.deactivate') : __('zerrocms.system_modules.activate') }}
                            </x-filament::button>
                            <x-filament::button
                                wire:click="uninstallSystemModule('{{ $safeKey }}')"
                                wire:confirm="{{ __('zerrocms.packages.delete_confirm', ['name' => $label]) }}"
                                color="danger"
                                size="sm"
                                outlined
                            >
                                {{ __('zerrocms.packages.delete') }}
                            </x-filament::button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>

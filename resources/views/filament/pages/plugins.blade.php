<x-filament-panels::page>
    @if($pluginsTableMissing ?? false)
        <div class="mb-4 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm">{{ __('zerrocms.plugins.table_missing') }}</div>
    @endif
    <p class="text-gray-500 dark:text-gray-400 mb-4">{{ __('zerrocms.plugins.intro') }}</p>
    @if(empty($plugins))
        <p class="text-gray-500 dark:text-gray-400">{{ __('zerrocms.plugins.none') }}</p>
    @else
        <div class="mb-4 flex justify-end">
            <x-filament::button wire:click="savePluginOrders" size="sm" color="gray" outlined>
                {{ __('zerrocms.plugins.save_order') }}
            </x-filament::button>
        </div>
        <ul class="space-y-3">
            @foreach($plugins as $key => $plugin)
                @php
                    $pluginName = $plugin['name'] ?? $key;
                    $hasConfig = !empty(app(\App\Support\PluginManager::class)->getConfigSchema($pluginName));
                @endphp
                <li class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="min-w-0 flex-1">
                        <span class="font-medium">{{ $pluginName }}</span>
                        @if(!empty($plugin['manifest']['version']))
                            <span class="text-sm text-gray-500 dark:text-gray-400">v{{ $plugin['manifest']['version'] }}</span>
                        @endif
                        @if(!empty($plugin['manifest']['description']))
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $plugin['manifest']['description'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                            <span>{{ __('zerrocms.plugins.order') }}</span>
                            <input type="number" wire:model="pluginOrders.{{ $key }}" min="0" class="fi-input block w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                        </label>
                        @if($hasConfig)
                            <x-filament::button
                                wire:click="openPluginConfig('{{ str_replace(['\\', chr(39)], ['\\\\', '\\' . chr(39)], $pluginName) }}')"
                                color="gray"
                                size="sm"
                                outlined
                            >
                                {{ __('zerrocms.plugins.configure') }}
                            </x-filament::button>
                        @endif
                        <x-filament::button
                            wire:click="togglePlugin('{{ str_replace(['\\', chr(39)], ['\\\\', '\\' . chr(39)], $pluginName) }}')"
                            :color="($plugin['enabled'] ?? false) ? 'danger' : 'success'"
                            size="sm"
                        >
                            {{ ($plugin['enabled'] ?? false) ? __('zerrocms.plugins.deactivate') : __('zerrocms.plugins.activate') }}
                        </x-filament::button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    @if($configuringPlugin ?? null)
        <div
            class="fi-modal fi-modal-open fi-absolute-positioning-context"
            role="dialog"
            aria-modal="true"
            x-data="{ open: @entangle('configuringPlugin') }"
            x-show="open"
            x-cloak
            x-transition:enter="fi-transition-enter"
            x-transition:leave="fi-transition-leave"
        >
            <div class="fi-modal-close-overlay" x-show="open" x-transition.duration.300ms.opacity wire:click="closePluginConfig"></div>
            <div class="fi-modal-window-ctn fi-clickable" wire:click.self="closePluginConfig">
                <div class="fi-modal-window fi-modal-window-has-close-btn fi-modal-window-has-content fi-modal-window-has-footer fi-width-lg fi-align-center">
                    <div class="fi-modal-header fi-vertical-align-center">
                        <x-filament::icon-button
                            color="gray"
                            icon="heroicon-o-x-mark"
                            icon-size="lg"
                            :label="__('zerrocms.plugins.close')"
                            wire:click="closePluginConfig"
                            class="fi-modal-close-btn"
                        />
                        <h2 class="fi-modal-heading">{{ __('zerrocms.plugins.configure_title', ['name' => $configuringPlugin]) }}</h2>
                    </div>
                    <form wire:submit="savePluginConfig" class="fi-modal-content space-y-4 p-6">
                        @foreach($pluginConfigSchema ?? [] as $item)
                            @php
                                $key = $item['key'] ?? '';
                                $type = $item['type'] ?? 'text';
                                $label = $item['label'] ?? $key;
                            @endphp
                            @if($type === 'boolean')
                                <x-filament::input.wrapper>
                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-2">
                                        <x-filament::input.checkbox
                                            wire:model="pluginConfigForm.{{ $key }}"
                                        />
                                        <span class="fi-fo-field-wrp-label-text text-sm font-medium text-gray-950 dark:text-white">{{ $label }}</span>
                                    </label>
                                </x-filament::input.wrapper>
                            @else
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : 'text') }}"
                                        wire:model="pluginConfigForm.{{ $key }}"
                                        :label="$label"
                                    />
                                </x-filament::input.wrapper>
                            @endif
                        @endforeach
                        <div class="fi-modal-footer flex justify-end gap-2 pt-4">
                            <x-filament::button type="button" color="gray" wire:click="closePluginConfig">
                                {{ __('zerrocms.plugins.cancel') }}
                            </x-filament::button>
                            <x-filament::button type="submit">
                                {{ __('zerrocms.plugins.save') }}
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

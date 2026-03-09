<x-filament-panels::page>
    @if($modulesTableMissing ?? false)
        <div class="mb-4 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm">{{ __('zerrocms.modules.table_missing') }}</div>
    @endif
    <p class="text-gray-500 dark:text-gray-400 mb-4">{{ __('zerrocms.modules.intro') }}</p>
    @if(empty($modules))
        <p class="text-gray-500 dark:text-gray-400">{{ __('zerrocms.modules.none') }}</p>
    @else
        <ul class="space-y-3">
            @foreach($modules as $key => $module)
                @php
                    $moduleName = $module['name'] ?? $key;
                    $hasConfig = !empty(app(\App\Support\ModuleManager::class)->getConfigSchema($key));
                @endphp
                <li class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div>
                        <span class="font-medium">{{ $moduleName }}</span>
                        @if(!empty($module['version']))
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">v{{ $module['version'] }}</span>
                        @endif
                        @if(!empty($module['description']))
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $module['description'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($hasConfig)
                            <x-filament::button
                                wire:click="openModuleConfig('{{ str_replace(['\\', chr(39)], ['\\\\', '\\' . chr(39)], $key) }}')"
                                color="gray"
                                size="sm"
                                outlined
                            >
                                {{ __('zerrocms.modules.configure') }}
                            </x-filament::button>
                        @endif
                        <x-filament::button
                            wire:click="toggleModule('{{ str_replace(['\\', chr(39)], ['\\\\', '\\' . chr(39)], $key) }}')"
                            :color="($module['enabled'] ?? false) ? 'danger' : 'success'"
                            size="sm"
                        >
                            {{ ($module['enabled'] ?? false) ? __('zerrocms.modules.deactivate') : __('zerrocms.modules.activate') }}
                        </x-filament::button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    @if($configuringModule ?? null)
        <div
            class="fi-modal fi-modal-open fi-absolute-positioning-context"
            role="dialog"
            aria-modal="true"
            x-data="{ open: @entangle('configuringModule') }"
            x-show="open"
            x-cloak
            x-transition:enter="fi-transition-enter"
            x-transition:leave="fi-transition-leave"
        >
            <div class="fi-modal-close-overlay" x-show="open" x-transition.duration.300ms.opacity wire:click="closeModuleConfig"></div>
            <div class="fi-modal-window-ctn fi-clickable" wire:click.self="closeModuleConfig">
                <div class="fi-modal-window fi-modal-window-has-close-btn fi-modal-window-has-content fi-modal-window-has-footer fi-width-lg fi-align-center">
                    <div class="fi-modal-header fi-vertical-align-center">
                        <x-filament::icon-button
                            color="gray"
                            icon="heroicon-o-x-mark"
                            icon-size="lg"
                            :label="__('zerrocms.modules.close')"
                            wire:click="closeModuleConfig"
                            class="fi-modal-close-btn"
                        />
                        <h2 class="fi-modal-heading">{{ __('zerrocms.modules.configure_title', ['name' => $configuringModule]) }}</h2>
                    </div>
                    <form wire:submit="saveModuleConfig" class="fi-modal-content space-y-4 p-6">
                        @foreach($moduleConfigSchema ?? [] as $item)
                            @php
                                $key = $item['key'] ?? '';
                                $type = $item['type'] ?? 'text';
                                $label = $item['label'] ?? $key;
                            @endphp
                            @if($type === 'boolean')
                                <x-filament::input.wrapper>
                                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-2">
                                        <x-filament::input.checkbox
                                            wire:model="moduleConfigForm.{{ $key }}"
                                        />
                                        <span class="fi-fo-field-wrp-label-text text-sm font-medium text-gray-950 dark:text-white">{{ $label }}</span>
                                    </label>
                                </x-filament::input.wrapper>
                            @else
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : 'text') }}"
                                        wire:model="moduleConfigForm.{{ $key }}"
                                        :label="$label"
                                    />
                                </x-filament::input.wrapper>
                            @endif
                        @endforeach
                        <div class="fi-modal-footer flex justify-end gap-2 pt-4">
                            <x-filament::button type="button" color="gray" wire:click="closeModuleConfig">
                                {{ __('zerrocms.modules.cancel') }}
                            </x-filament::button>
                            <x-filament::button type="submit">
                                {{ __('zerrocms.modules.save') }}
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

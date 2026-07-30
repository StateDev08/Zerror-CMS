<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>{{ __('update_db.intro') }}</p>
            <p class="zc-help-tip">{{ __('update_db.help') }}</p>
        </div>

        <section class="zc-section">
            <h3 class="zc-section__title">{{ __('update_db.options') }}</h3>
            <p class="zc-section__desc">{{ __('update_db.options_desc') }}</p>

            <div class="zc-fields" style="margin-top:1rem">
                <label class="zc-check">
                    <input type="checkbox" checked disabled>
                    <span>
                        <strong>{{ __('update_db.opt_migrate') }}</strong>
                        <span>{{ __('update_db.opt_migrate_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="syncPermissions">
                    <span>
                        <strong>{{ __('update_db.opt_permissions') }}</strong>
                        <span>{{ __('update_db.opt_permissions_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="syncLegalPages">
                    <span>
                        <strong>{{ __('update_db.opt_legal') }}</strong>
                        <span>{{ __('update_db.opt_legal_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="syncMenus">
                    <span>
                        <strong>{{ __('update_db.opt_menus') }}</strong>
                        <span>{{ __('update_db.opt_menus_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="syncHomeWidgets">
                    <span>
                        <strong>{{ __('update_db.opt_widgets') }}</strong>
                        <span>{{ __('update_db.opt_widgets_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="clearCaches">
                    <span>
                        <strong>{{ __('update_db.opt_cache') }}</strong>
                        <span>{{ __('update_db.opt_cache_help') }}</span>
                    </span>
                </label>
            </div>

            <div class="zc-actions" style="margin-top:1.25rem">
                <x-filament::button
                    wire:click="runUpdate"
                    wire:loading.attr="disabled"
                    wire:target="runUpdate"
                    icon="heroicon-o-arrow-path"
                    color="primary"
                >
                    <span wire:loading.remove wire:target="runUpdate">{{ __('update_db.run') }}</span>
                    <span wire:loading wire:target="runUpdate">{{ __('update_db.running') }}</span>
                </x-filament::button>
            </div>
        </section>

        @if($lastOk !== null)
            <section class="zc-section" style="margin-top:1.25rem">
                <h3 class="zc-section__title">{{ __('update_db.result') }}</h3>
                <div class="zc-alert {{ $lastOk ? 'zc-alert--ok' : 'zc-alert--err' }}" style="margin-bottom:1rem">
                    {{ $lastOk ? __('update_db.success') : __('update_db.failed') }}
                </div>
                <div class="zc-field-stack">
                    @foreach($lastSteps as $step)
                        <div class="zc-section" style="padding:0.85rem 1rem">
                            <p style="margin:0 0 0.35rem;font-weight:600">
                                {{ $step['ok'] ? '✓' : '✗' }} {{ $step['label'] }}
                            </p>
                            @if(($step['detail'] ?? '') !== '')
                                <pre style="margin:0;white-space:pre-wrap;font-size:0.78rem;opacity:0.85;max-height:12rem;overflow:auto">{{ $step['detail'] }}</pre>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-filament-panels::page>

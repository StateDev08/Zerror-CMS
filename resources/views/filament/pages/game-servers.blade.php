<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>{{ __('servers.acp_intro') }}</p>
            <p class="zc-help-tip">{{ __('servers.acp_tip', ['url' => url('/servers')]) }}</p>
        </div>

        <form wire:submit="save" class="zc-field-stack">
            <section class="zc-section">
                <div class="zc-section__head">
                    <h2 class="zc-section__title">{{ __('servers.acp_page_settings') }}</h2>
                    <p class="zc-section__desc">{{ __('servers.acp_page_settings_help') }}</p>
                </div>
                <div class="zc-fields--2">
                    <div class="zc-field">
                        <label for="page_title">{{ __('servers.acp_page_title') }}</label>
                        <input id="page_title" class="zc-input" type="text" wire:model="page_title">
                    </div>
                    <div class="zc-field">
                        <label for="widget_title">{{ __('servers.acp_widget_title') }}</label>
                        <input id="widget_title" class="zc-input" type="text" wire:model="widget_title">
                    </div>
                    <div class="zc-field" style="grid-column: 1 / -1">
                        <label for="page_intro">{{ __('servers.acp_page_intro') }}</label>
                        <textarea id="page_intro" class="zc-textarea" rows="2" wire:model="page_intro"></textarea>
                    </div>
                    <div class="zc-field">
                        <label for="cache_seconds">{{ __('servers.acp_cache') }}</label>
                        <input id="cache_seconds" class="zc-input" type="number" min="0" wire:model="cache_seconds">
                    </div>
                    <div class="zc-field">
                        <label for="timeout">{{ __('servers.acp_timeout') }}</label>
                        <input id="timeout" class="zc-input" type="number" min="0.5" step="0.1" wire:model="timeout">
                    </div>
                </div>
            </section>

            <section class="zc-section">
                <div class="zc-section__head" style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                    <div>
                        <h2 class="zc-section__title">{{ __('servers.acp_list_title') }}</h2>
                        <p class="zc-section__desc">{{ __('servers.acp_list_help') }}</p>
                    </div>
                    <x-filament::button type="button" color="gray" wire:click="addServer" icon="heroicon-o-plus">
                        {{ __('servers.acp_add') }}
                    </x-filament::button>
                </div>

                <div class="zc-server-cards">
                    @foreach($servers as $index => $server)
                        <article class="zc-server-card" wire:key="server-{{ $index }}">
                            <div class="zc-server-card__head">
                                <strong>{{ __('servers.acp_server_n', ['n' => $index + 1]) }}</strong>
                                <x-filament::button type="button" color="danger" size="sm" outlined wire:click="removeServer({{ $index }})">
                                    {{ __('servers.acp_remove') }}
                                </x-filament::button>
                            </div>

                            <div class="zc-fields--2">
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_label') }}</label>
                                    <input class="zc-input" type="text" wire:model="servers.{{ $index }}.label" placeholder="Survival #1">
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_game') }}</label>
                                    <input class="zc-input" type="text" wire:model="servers.{{ $index }}.game" placeholder="Minecraft">
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_mod_type') }}</label>
                                    <select class="zc-select" wire:model="servers.{{ $index }}.mod_type">
                                        <option value="vanilla">{{ __('servers.mod_vanilla') }}</option>
                                        <option value="modded">{{ __('servers.mod_modded') }}</option>
                                    </select>
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_host') }}</label>
                                    <input class="zc-input" type="text" wire:model="servers.{{ $index }}.host" placeholder="play.example.com" required>
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_type') }}</label>
                                    <select class="zc-select" wire:model="servers.{{ $index }}.type">
                                        <option value="auto">Auto</option>
                                        <option value="minecraft">Minecraft Java</option>
                                        <option value="bedrock">Minecraft Bedrock</option>
                                        <option value="source">Source / Steam</option>
                                        <option value="tcp">TCP (nur Online/Offline)</option>
                                    </select>
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_port') }}</label>
                                    <input class="zc-input" type="number" min="1" max="65535" wire:model="servers.{{ $index }}.port">
                                </div>
                                <div class="zc-field">
                                    <label>{{ __('servers.acp_query_port') }}</label>
                                    <input class="zc-input" type="number" min="1" max="65535" wire:model="servers.{{ $index }}.query_port">
                                    <span class="zc-field-help">{{ __('servers.acp_query_port_help') }}</span>
                                </div>
                                <div class="zc-field" style="grid-column: 1 / -1">
                                    <label>{{ __('servers.acp_banner') }}</label>
                                    @php $preview = $this->bannerPreviewUrl($index); @endphp
                                    @if($preview)
                                        <div class="zc-server-banner-preview">
                                            <img src="{{ $preview }}" alt="">
                                            <x-filament::button type="button" size="sm" color="danger" outlined wire:click="removeBanner({{ $index }})">
                                                {{ __('servers.acp_banner_remove') }}
                                            </x-filament::button>
                                        </div>
                                    @endif
                                    <input class="zc-file" type="file" accept="image/*" wire:model="bannerUploads.{{ $index }}">
                                    <div wire:loading wire:target="bannerUploads.{{ $index }}" class="zc-field-help">{{ __('servers.acp_banner_uploading') }}</div>
                                    <span class="zc-field-help">{{ __('servers.acp_banner_help') }}</span>
                                    <label class="zc-field-help" style="display:block;margin-top:0.5rem">{{ __('servers.acp_banner_url_optional') }}</label>
                                    <input class="zc-input" type="url" wire:model="servers.{{ $index }}.banner" placeholder="https://…/banner.jpg">
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div style="margin-top: 1rem">
                    <x-filament::button type="button" color="gray" wire:click="addServer" icon="heroicon-o-plus">
                        {{ __('servers.acp_add') }}
                    </x-filament::button>
                </div>
            </section>

            <div class="zc-modal__footer" style="position:static;border:0;padding:0;justify-content:flex-start">
                <x-filament::button type="submit">{{ __('servers.acp_save') }}</x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>

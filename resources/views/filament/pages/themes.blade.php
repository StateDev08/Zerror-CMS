<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>{{ __('zerrocms.themes.intro', ['active' => $active]) }}</p>
            <p class="zc-help-tip">
                {{ __('zerrocms.themes.help_page') }}
                <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
            </p>
            @include('filament.partials.zc-context-links')
        </div>

        <h3 class="zc-section__title">{{ __('zerrocms.themes.installed_title') }}</h3>

        @if(empty($themes))
            <div class="zc-empty">
                <h3 class="zc-empty__title">{{ __('zerrocms.themes.none') }}</h3>
                <p class="zc-empty__hint">
                    <a href="{{ $installerUrl }}" class="zc-inline-link">{{ __('zerrocms.packages.open_installer') }}</a>
                </p>
            </div>
        @else
            <div class="zc-grid zc-grid--3">
                @foreach($themes as $key => $theme)
                    @php
                        $themeName = $theme['name'] ?? $key;
                        $isActive = ($active ?? '') === $themeName;
                        $colors = $theme['colors'] ?? [];
                        $safeTheme = str_replace(['\\', "'"], ['\\\\', "\\'"], $themeName);
                        $isBuiltin = (bool) ($themeMeta[$themeName]['builtin'] ?? false);
                    @endphp
                    <article class="zc-card {{ $isActive ? 'zc-card--active' : '' }}">
                        <div class="zc-card__head">
                            <h3 class="zc-card__title">{{ $theme['label'] ?? $themeName }}</h3>
                            @if(!empty($theme['version']))
                                <span class="zc-card__meta">v{{ $theme['version'] }}</span>
                            @endif
                            @if($isActive)
                                <span class="zc-badge zc-badge--ok">{{ __('zerrocms.status.active') }}</span>
                            @endif
                            @if($isBuiltin)
                                <span class="zc-badge zc-badge--off">{{ __('zerrocms.themes.builtin') }}</span>
                            @endif
                        </div>
                        @if(!empty($theme['description']))
                            <p class="zc-card__desc">{{ $theme['description'] }}</p>
                        @endif
                        <p class="zc-card__meta">ID: <code>{{ $themeName }}</code></p>
                        @if(!empty($colors))
                            <div class="zc-swatches" aria-hidden="true">
                                @foreach(array_slice($colors, 0, 6) as $hex)
                                    <span class="zc-swatch" style="background: {{ $hex }}"></span>
                                @endforeach
                            </div>
                        @endif
                        <div class="zc-card__actions" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center">
                            @if($isActive)
                                <span class="zc-card__meta">{{ __('zerrocms.themes.currently_active') }}</span>
                            @else
                                <x-filament::button wire:click="setTheme('{{ $safeTheme }}')" color="primary" size="sm">
                                    {{ __('zerrocms.themes.choose') }}
                                </x-filament::button>
                            @endif
                            @if(! $isBuiltin && ! $isActive)
                                <x-filament::button
                                    wire:click="deleteCustomTheme('{{ $safeTheme }}')"
                                    wire:confirm="{{ __('zerrocms.themes.delete_confirm', ['name' => $themeName]) }}"
                                    color="danger"
                                    size="sm"
                                >
                                    {{ __('zerrocms.themes.delete') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>

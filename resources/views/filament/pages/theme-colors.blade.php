<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>{{ __('zerrocms.theme_editor.intro') }}</p>
            <p class="zc-help-tip">{{ __('zerrocms.theme_editor.help_page') }}</p>
            @include('filament.partials.zc-context-links')
        </div>

        <form wire:submit="save" class="zc-page">
            <x-filament::section>
                <x-slot name="heading">{{ __('zerrocms.theme_editor.colors_heading') }}</x-slot>
                <x-slot name="description">{{ __('zerrocms.theme_editor.colors_help') }}</x-slot>
                <div class="zc-color-grid">
                    @foreach([
                        'primary' => ['label' => __('zerrocms.theme_editor.primary'), 'help' => __('zerrocms.theme_editor.primary_help'), 'ph' => '#3b82f6'],
                        'accent' => ['label' => __('zerrocms.theme_editor.accent'), 'help' => __('zerrocms.theme_editor.accent_help'), 'ph' => '#10b981'],
                        'background' => ['label' => __('zerrocms.theme_editor.background'), 'help' => __('zerrocms.theme_editor.background_help'), 'ph' => '#0c100e'],
                        'surface' => ['label' => __('zerrocms.theme_editor.surface'), 'help' => __('zerrocms.theme_editor.surface_help'), 'ph' => '#161c18'],
                        'text' => ['label' => __('zerrocms.theme_editor.text'), 'help' => __('zerrocms.theme_editor.text_help'), 'ph' => '#e8f5e9'],
                        'text_muted' => ['label' => __('zerrocms.theme_editor.text_muted'), 'help' => __('zerrocms.theme_editor.text_muted_help'), 'ph' => '#9cba9f'],
                    ] as $field => $meta)
                        <div class="zc-color-item">
                            <label>{{ $meta['label'] }}</label>
                            <div class="zc-field-help">{{ $meta['help'] }}</div>
                            <div class="zc-color-row">
                                <input type="color" wire:model.live="{{ $field }}" aria-label="{{ $meta['label'] }}">
                                <input type="text" wire:model.live="{{ $field }}" placeholder="{{ $meta['ph'] }}" maxlength="7" aria-label="{{ $meta['label'] }} Hex">
                            </div>
                            @error($field) <span style="color:#dc2626;font-size:0.8rem">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">{{ __('zerrocms.theme_editor.fonts_heading') }}</x-slot>
                <x-slot name="description">{{ __('zerrocms.theme_editor.fonts_help') }}</x-slot>
                <div class="zc-color-grid">
                    <div class="zc-field">
                        <label for="font_display">{{ __('zerrocms.theme_editor.font_display') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.font_display_help') }}</span>
                        <input id="font_display" type="text" wire:model="font_display" maxlength="120">
                        @error('font_display') <span style="color:#dc2626;font-size:0.8rem">{{ $message }}</span> @enderror
                    </div>
                    <div class="zc-field">
                        <label for="font_body">{{ __('zerrocms.theme_editor.font_body') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.font_body_help') }}</span>
                        <input id="font_body" type="text" wire:model="font_body" maxlength="120">
                        @error('font_body') <span style="color:#dc2626;font-size:0.8rem">{{ $message }}</span> @enderror
                    </div>
                    <div class="zc-field" style="grid-column:1/-1">
                        <label for="font_url">{{ __('zerrocms.theme_editor.font_url') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.font_url_help') }}</span>
                        <input id="font_url" type="url" wire:model="font_url" maxlength="500" placeholder="https://fonts.googleapis.com/css2?...">
                        @error('font_url') <span style="color:#dc2626;font-size:0.8rem">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">{{ __('zerrocms.theme_editor.mode_heading') }}</x-slot>
                <x-slot name="description">{{ __('zerrocms.theme_editor.mode_help') }}</x-slot>
                            <div class="zc-field" style="max-width:24rem">
                                <label for="default_theme_mode">{{ __('zerrocms.theme_editor.mode_label') }}</label>
                                <span class="zc-field-help">Gilt für Frontend und Admin-Panel. Bei „Dunkel“ bleibt der Admin nach F5 dunkel.</span>
                                <select id="default_theme_mode" wire:model="default_theme_mode">
                                    <option value="system">{{ __('zerrocms.theme_editor.mode_system') }}</option>
                                    <option value="light">{{ __('zerrocms.theme_editor.mode_light') }}</option>
                                    <option value="dark">{{ __('zerrocms.theme_editor.mode_dark') }}</option>
                                </select>
                            </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">{{ __('zerrocms.theme_editor.layout_heading') }}</x-slot>
                <x-slot name="description">{{ __('zerrocms.theme_editor.layout_help') }}</x-slot>
                <div class="zc-color-grid">
                    <div class="zc-field">
                        <label for="nav_sidebar_position">{{ __('zerrocms.theme_editor.nav_sidebar') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.nav_sidebar_help') }}</span>
                        <select id="nav_sidebar_position" wire:model="nav_sidebar_position">
                            <option value="left">{{ __('zerrocms.theme_editor.left') }}</option>
                            <option value="right">{{ __('zerrocms.theme_editor.right') }}</option>
                        </select>
                    </div>
                    <div class="zc-field">
                        <label for="widget_sidebar_position">{{ __('zerrocms.theme_editor.widget_sidebar') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.widget_sidebar_help') }}</span>
                        <select id="widget_sidebar_position" wire:model="widget_sidebar_position">
                            <option value="left">{{ __('zerrocms.theme_editor.left') }}</option>
                            <option value="right">{{ __('zerrocms.theme_editor.right') }}</option>
                        </select>
                    </div>
                    <div class="zc-field">
                        <label for="main_order">{{ __('zerrocms.theme_editor.main_order') }}</label>
                        <span class="zc-field-help">{{ __('zerrocms.theme_editor.main_order_help') }}</span>
                        <select id="main_order" wire:model="main_order">
                            <option value="content_first">{{ __('zerrocms.theme_editor.content_first') }}</option>
                            <option value="widgets_first">{{ __('zerrocms.theme_editor.widgets_first') }}</option>
                        </select>
                    </div>
                </div>
                <div class="zc-note" style="margin-top:0.85rem">{{ __('zerrocms.theme_editor.layout_note') }}</div>
            </x-filament::section>

            <x-filament::button type="submit" wire:loading.attr="disabled">
                {{ __('zerrocms.theme_editor.save') }}
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>

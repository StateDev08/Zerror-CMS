<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>{{ __('zerrocms.packages.page_intro') }}</p>
            @include('filament.partials.zc-glossary')
            <p class="zc-help-tip">{{ __('zerrocms.packages.page_tip') }}</p>
            @include('filament.partials.zc-context-links')
        </div>

        <section class="zc-section">
            <h3 class="zc-section__title">{{ __('zerrocms.packages.install_title') }}</h3>
            <p class="zc-section__desc">{{ __('zerrocms.packages.install_desc') }}</p>

            <form wire:submit="installPackageZip" class="zc-fields">
                <div class="zc-field">
                    <label for="package-type">{{ __('zerrocms.packages.type_label') }}</label>
                    <select id="package-type" class="zc-select" wire:model="packageType">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="zc-field-help">{{ __('zerrocms.packages.type_help') }}</span>
                </div>

                <div class="zc-field">
                    <label for="package-zip">{{ __('zerrocms.packages.zip_label') }}</label>
                    <input id="package-zip" class="zc-file" type="file" wire:model="packageZip" accept=".zip,application/zip">
                    <span class="zc-field-help">{{ __('zerrocms.packages.zip_hint', ['mb' => $uploadMaxMb ?? 40]) }}</span>
                    <div wire:loading wire:target="packageZip" class="zc-field-help">{{ __('zerrocms.packages.uploading') }}</div>
                </div>

                <label class="zc-check">
                    <input type="checkbox" wire:model="enableAfterInstall">
                    <span>
                        <strong>{{ __('zerrocms.packages.enable_after') }}</strong>
                        <span>{{ __('zerrocms.packages.enable_after_help') }}</span>
                    </span>
                </label>

                <label class="zc-check">
                    <input type="checkbox" wire:model="overwritePackage">
                    <span>
                        <strong>{{ __('zerrocms.packages.overwrite') }}</strong>
                        <span>{{ __('zerrocms.packages.overwrite_help') }}</span>
                    </span>
                </label>

                <div class="zc-actions" style="padding-top:0.25rem">
                    <x-filament::button type="submit">{{ __('zerrocms.packages.install') }}</x-filament::button>
                </div>
            </form>
        </section>

        <section class="zc-section">
            <h3 class="zc-section__title">{{ __('zerrocms.packages.examples_title') }}</h3>
            <p class="zc-section__desc">{{ __('zerrocms.packages.examples_desc') }}</p>
            <div class="zc-actions" style="padding-top:0;flex-wrap:wrap">
                @foreach(['module','plugin','widget','theme'] as $exType)
                    @if(!empty($examples[$exType]))
                        <x-filament::button type="button" color="gray" outlined size="sm" wire:click="downloadExample('{{ $exType }}')">
                            {{ __('zerrocms.packages.download_example_'.$exType) }}
                        </x-filament::button>
                    @endif
                @endforeach
            </div>
        </section>
    </div>
</x-filament-panels::page>

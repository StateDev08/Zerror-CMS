{{--
  Erwartet: $schema (list), $formModel (z.B. "moduleConfigForm" oder "pluginConfigForm")
--}}
@foreach($schema ?? [] as $item)
    @php
        if (! is_array($item)) {
            continue;
        }
        $key = (string) ($item['key'] ?? '');
        if ($key === '') {
            continue;
        }
        $type = (string) ($item['type'] ?? 'text');
        $label = (string) ($item['label'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $key)));
        $help = (string) ($item['help'] ?? $item['description'] ?? $item['hint'] ?? '');
        $placeholder = (string) ($item['placeholder'] ?? '');
        $required = (bool) ($item['required'] ?? false);
        $model = $formModel.'.'.$key;
        $inputId = $formModel.'-'.$key;
        $currentVal = data_get($this->{$formModel} ?? [], $key);
    @endphp

    @if($type === 'boolean' || $type === 'checkbox')
        <label class="zc-check" for="{{ $inputId }}">
            <input id="{{ $inputId }}" type="checkbox" wire:model="{{ $model }}">
            <span>
                <strong>{{ $label }}@if($required) * @endif</strong>
                @if($help !== '')
                    <span>{{ $help }}</span>
                @endif
            </span>
        </label>
    @elseif($type === 'file' || $type === 'audio')
        <div class="zc-field">
            <label for="{{ $inputId }}">{{ $label }}@if($required) * @endif</label>
            @if($help !== '')
                <span class="zc-field-help">{{ $help }}</span>
            @endif
            @if(is_string($currentVal) && $currentVal !== '')
                <div class="zc-file-current" style="margin:0.4rem 0 0.65rem;display:flex;flex-direction:column;gap:0.45rem">
                    <span class="zc-field-help">{{ __('zerrocms.modules.current_file') }}: <code>{{ $currentVal }}</code></span>
                    @if($type === 'audio')
                        <audio controls preload="metadata" src="{{ storage_asset(ltrim($currentVal, '/')) }}" style="width:100%;max-width:22rem"></audio>
                    @endif
                    @if(method_exists($this, 'clearModuleFile'))
                    <div>
                        <x-filament::button type="button" size="sm" color="danger" outlined wire:click="clearModuleFile('{{ $key }}')">
                            {{ __('zerrocms.modules.remove_file') }}
                        </x-filament::button>
                    </div>
                    @endif
                </div>
            @endif
            <input
                id="{{ $inputId }}"
                type="file"
                wire:model="{{ ($formModel === 'moduleConfigForm') ? 'moduleFileUploads.'.$key : $model }}"
                @if($type === 'audio') accept="audio/mpeg,audio/mp3,.mp3" @endif
            >
            <div wire:loading wire:target="{{ ($formModel === 'moduleConfigForm') ? 'moduleFileUploads.'.$key : $model }}" class="zc-field-help">{{ __('zerrocms.modules.uploading') }}</div>
            @error(($formModel === 'moduleConfigForm') ? 'moduleFileUploads.'.$key : $model) <span class="zc-field-help" style="color:#f87171">{{ $message }}</span> @enderror
            <span class="zc-field-help">{{ __('settings.upload_limit_hint', ['mb' => $uploadMaxFileMb ?? \App\Support\UploadLimits::fileMb()]) }}</span>
        </div>
    @else
        <div class="zc-field">
            <label for="{{ $inputId }}">{{ $label }}@if($required) * @endif</label>
            @if($help !== '')
                <span class="zc-field-help">{{ $help }}</span>
            @endif
            @if($type === 'textarea')
                <textarea id="{{ $inputId }}" wire:model="{{ $model }}" rows="{{ (int) ($item['rows'] ?? 4) }}" placeholder="{{ $placeholder }}"></textarea>
            @elseif($type === 'select' && !empty($item['options']) && is_array($item['options']))
                <select id="{{ $inputId }}" wire:model="{{ $model }}">
                    @foreach($item['options'] as $optValue => $optLabel)
                        <option value="{{ is_int($optValue) ? $optLabel : $optValue }}">{{ $optLabel }}</option>
                    @endforeach
                </select>
            @else
                <input
                    id="{{ $inputId }}"
                    type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : ($type === 'email' ? 'email' : 'text')) }}"
                    wire:model="{{ $model }}"
                    placeholder="{{ $placeholder }}"
                    @if($type === 'number') min="{{ $item['min'] ?? 0 }}" max="{{ $item['max'] ?? 100 }}" @endif
                >
            @endif
        </div>
    @endif
@endforeach

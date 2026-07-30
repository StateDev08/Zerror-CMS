@assets
    {{-- Dieselbe Filament-CSS wie im ACP — ohne app.css ist die Toolbar unsichtbar --}}
    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}?v=5.3.2.0" data-navigate-track>
    <link rel="stylesheet" href="{{ asset('fonts/filament/filament/inter/index.css') }}?v=5.3.2.0" data-navigate-track>
    <link rel="stylesheet" href="{{ asset('css/zc-admin.css') }}?v=8">
    @filamentStyles
    <style>
        .cms-frontend-rich-editor { color-scheme: dark; }
        .cms-frontend-rich-editor .fi-fo-field-label {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        .cms-frontend-rich-editor .fi-input-wrp,
        .cms-frontend-rich-editor .fi-fo-rich-editor {
            border-radius: var(--theme-widget-radius, 0.5rem);
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--theme-primary) 35%, transparent);
            background: color-mix(in srgb, var(--theme-surface) 90%, #000);
        }
        /* Fallback falls Theme-CSS Toolbar nicht layoutet */
        .cms-frontend-rich-editor .fi-fo-rich-editor-toolbar {
            display: flex !important;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem;
            padding: 0.45rem 0.6rem;
            border-bottom: 1px solid color-mix(in srgb, var(--theme-primary) 25%, transparent);
            background: color-mix(in srgb, var(--theme-surface) 96%, #000);
        }
        .cms-frontend-rich-editor .fi-fo-rich-editor-toolbar-group {
            display: inline-flex !important;
            flex-wrap: wrap;
            gap: 0.1rem;
            align-items: center;
        }
        .cms-frontend-rich-editor .fi-fo-rich-editor-tool {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            min-height: 2rem;
            padding: 0.3rem;
            border-radius: 0.375rem;
            color: var(--theme-primary) !important;
            background: transparent;
            border: 0;
            cursor: pointer;
        }
        .cms-frontend-rich-editor .fi-fo-rich-editor-tool:hover,
        .cms-frontend-rich-editor .fi-fo-rich-editor-tool.fi-active {
            background: color-mix(in srgb, var(--theme-primary) 20%, transparent);
        }
        .cms-frontend-rich-editor .fi-fo-rich-editor-tool svg {
            width: 1.1rem;
            height: 1.1rem;
            display: block;
        }
        .cms-frontend-rich-editor .fi-fo-rich-editor-content {
            background: color-mix(in srgb, var(--theme-surface) 88%, #000);
        }
        .cms-frontend-rich-editor .ProseMirror {
            color: var(--theme-text) !important;
            min-height: 10rem;
            padding: 0.75rem 1rem;
            outline: none;
        }
        .cms-frontend-rich-editor .ProseMirror p { margin: 0 0 0.75em; }
    </style>
    @filamentScripts(withCore: true)
@endassets

@php
    $htmlContent = '';
    try {
        $htmlContent = (string) ($this->form->getState()['content'] ?? '');
    } catch (\Throwable) {
        $htmlContent = '';
    }
@endphp

<div
    class="cms-frontend-rich-editor dark fi zc-rich-editor"
    wire:ignore.self
    data-cms-rich-editor
>
    <input type="hidden" name="{{ $name }}" value="{{ $htmlContent }}">
    {{ $this->form }}
    <x-filament-actions::modals />
</div>

@script
<script>
    if (! window.__cmsRichEditorSubmitBound) {
        window.__cmsRichEditorSubmitBound = true;
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (! (form instanceof HTMLFormElement)) return;
            form.querySelectorAll('[data-cms-rich-editor]').forEach(function (wrap) {
                var hidden = wrap.querySelector('input[type="hidden"]');
                var prose = wrap.querySelector('.ProseMirror');
                if (hidden && prose) hidden.value = prose.innerHTML;
            });
        }, true);
    }
</script>
@endscript

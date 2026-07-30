<?php

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Beispiel-Modul-Provider. Wird von ModuleManager per require geladen.
 * Rückgabe: Provider-Klassenname.
 */
if (! class_exists('ZerroExampleModuleWidget', false)) {
    class ZerroExampleModuleWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'example';
        }

        public function title(): string
        {
            return 'Example Module';
        }

        public function render(array $config = []): string
        {
            $merged = array_merge(module_config('example'), $config);
            $title = trim((string) ($merged['title'] ?? 'Example Module')) ?: 'Example Module';
            $text = trim((string) ($merged['text'] ?? ''));

            return '<aside class="cms-widget rounded-xl border border-white/10 bg-black/20 p-4 text-sm">'
                .'<h3 class="font-semibold mb-2">'.e($title).'</h3>'
                .($text !== '' ? '<p class="opacity-80">'.e($text).'</p>' : '')
                .'</aside>';
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Example Module'],
                'text' => ['type' => 'text', 'label' => 'Text', 'default' => ''],
            ];
        }
    }
}

if (! class_exists('ZerroExampleModuleServiceProvider', false)) {
    class ZerroExampleModuleServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            //
        }

        public function boot(): void
        {
            app(WidgetRegistry::class)->register(new ZerroExampleModuleWidget(), ['left', 'right']);
        }
    }
}

return ZerroExampleModuleServiceProvider::class;

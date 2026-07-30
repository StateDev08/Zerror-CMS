<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

/**
 * Beispiel-Plugin-Provider. Wird von PluginManager per require geladen.
 */
if (! class_exists('ZerroExamplePluginServiceProvider', false)) {
    class ZerroExamplePluginServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            //
        }

        public function boot(): void
        {
            $config = plugin_config('example');
            if (! filter_var($config['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                return;
            }
            $note = trim((string) ($config['note'] ?? 'ZerroCMS example plugin'));
            if ($note === '') {
                return;
            }

            app(PluginManager::class)->registerHeadHtml(function () use ($note) {
                return '<!-- '.e($note)." -->\n";
            });
        }
    }
}

return ZerroExamplePluginServiceProvider::class;

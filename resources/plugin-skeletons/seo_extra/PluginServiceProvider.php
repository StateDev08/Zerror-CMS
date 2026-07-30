<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroSeoExtraPluginServiceProvider', false)) {
    class ZerroSeoExtraPluginServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $cfg = plugin_config('seo_extra');
            if (! filter_var($cfg['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            app(PluginManager::class)->registerHeadHtml(function () use ($cfg) {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }

                $out = '';
                $keywords = trim((string) ($cfg['keywords'] ?? ''));
                if ($keywords !== '') {
                    $out .= '<meta name="keywords" content="'.e($keywords).'">'."\n";
                }

                foreach (preg_split('/\r\n|\r|\n/', (string) ($cfg['extra_meta'] ?? '')) as $line) {
                    $line = trim($line);
                    if ($line === '' || ! str_contains($line, '|')) {
                        continue;
                    }
                    [$name, $content] = array_map('trim', explode('|', $line, 2));
                    $name = preg_replace('/[^a-zA-Z0-9:_-]/', '', $name) ?? '';
                    if ($name === '' || $content === '') {
                        continue;
                    }
                    // Title/Description bewusst nicht überschreiben
                    if (in_array(strtolower($name), ['description', 'og:description', 'twitter:description'], true)) {
                        continue;
                    }
                    $out .= '<meta name="'.e($name).'" content="'.e($content).'">'."\n";
                }

                return $out;
            });
        }
    }
}

return ZerroSeoExtraPluginServiceProvider::class;

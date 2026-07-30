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
                // Core-SEO (seo-meta Partial) hat Vorrang — keine doppelten Keywords/Robots/OG-Locale
                $coreKeywords = trim((string) setting('seo_keywords', ''));
                $keywords = trim((string) ($cfg['keywords'] ?? ''));
                if ($keywords !== '' && $coreKeywords === '') {
                    $out .= '<meta name="keywords" content="'.e($keywords).'">'."\n";
                }

                $skipMeta = [
                    'description', 'og:description', 'twitter:description',
                    'keywords', 'robots', 'og:locale', 'og:title', 'og:site_name', 'og:url', 'og:type', 'og:image',
                ];

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
                    if (in_array(strtolower($name), $skipMeta, true)) {
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

<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroAnalyticsPluginServiceProvider', false)) {
    class ZerroAnalyticsPluginServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $cfg = plugin_config('analytics');
            if (! filter_var($cfg['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            app(PluginManager::class)->registerHeadHtml(function () use ($cfg) {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }

                $provider = strtolower(trim((string) ($cfg['provider'] ?? 'ga4')));
                $id = trim((string) ($cfg['measurement_id'] ?? ''));
                $scriptUrl = trim((string) ($cfg['script_url'] ?? ''));

                if ($provider === 'ga4' && $id !== '') {
                    $idEsc = e($id);

                    return '<script async src="https://www.googletagmanager.com/gtag/js?id='.$idEsc.'"></script>'
                        .'<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",'.json_encode($id).');</script>';
                }

                if ($provider === 'plausible' && $id !== '') {
                    $src = $scriptUrl !== '' ? $scriptUrl : 'https://plausible.io/js/script.js';

                    return '<script defer data-domain="'.e($id).'" src="'.e($src).'"></script>';
                }

                if ($provider === 'umami' && $id !== '' && $scriptUrl !== '') {
                    return '<script defer src="'.e($scriptUrl).'" data-website-id="'.e($id).'"></script>';
                }

                if ($provider === 'custom') {
                    $snippet = (string) ($cfg['custom_snippet'] ?? '');
                    // Keine script-closer-Injection durch doppelte Tags verhindern: nur erlauben wenn kein </script> im Snippet
                    if ($snippet === '' || stripos($snippet, '</script') !== false) {
                        return '';
                    }

                    return $snippet;
                }

                return '';
            });
        }
    }
}

return ZerroAnalyticsPluginServiceProvider::class;

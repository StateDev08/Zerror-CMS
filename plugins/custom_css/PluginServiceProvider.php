<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroCustomCssPluginServiceProvider', false)) {
    class ZerroCustomCssPluginServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $cfg = plugin_config('custom_css');
            if (! filter_var($cfg['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            app(PluginManager::class)->registerHeadHtml(function () use ($cfg) {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }
                $css = (string) ($cfg['css'] ?? '');
                $css = str_replace(['</style', '</STYLE'], '', $css);
                $css = trim($css);
                if ($css === '') {
                    return '';
                }

                return "<style id=\"zerrocms-custom-css\">\n".$css."\n</style>";
            });
        }
    }
}

return ZerroCustomCssPluginServiceProvider::class;

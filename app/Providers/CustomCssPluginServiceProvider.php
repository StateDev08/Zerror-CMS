<?php

namespace App\Providers;

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

class CustomCssPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $config = plugin_config('custom_css');
        $enabled = (bool) ($config['enabled'] ?? false);
        $css = trim((string) ($config['css_content'] ?? ''));
        if (! $enabled || $css === '') {
            return;
        }
        $css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $css);
        $css = e($css);
        app(PluginManager::class)->registerHeadHtml(function () use ($css) {
            return "<style>\n" . $css . "\n</style>";
        });
    }
}

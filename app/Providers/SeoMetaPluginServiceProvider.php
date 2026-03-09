<?php

namespace App\Providers;

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

class SeoMetaPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $config = plugin_config('seo_meta');
        $description = trim((string) ($config['default_description'] ?? ''));
        $keywords = trim((string) ($config['default_keywords'] ?? ''));
        if ($description === '' && $keywords === '') {
            return;
        }
        app(PluginManager::class)->registerHeadHtml(function () use ($description, $keywords) {
            $out = '';
            if ($description !== '') {
                $out .= '<meta name="description" content="' . e($description) . '">' . "\n";
            }
            if ($keywords !== '') {
                $out .= '<meta name="keywords" content="' . e($keywords) . '">' . "\n";
            }
            return $out;
        });
    }
}

<?php

namespace App\Providers;

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

class AnalyticsPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $config = plugin_config('analytics');
        $enabled = (bool) ($config['enabled'] ?? false);
        $trackingId = trim((string) ($config['tracking_id'] ?? ''));
        if (! $enabled || $trackingId === '') {
            return;
        }
        $trackingId = e($trackingId);
        app(PluginManager::class)->registerHeadHtml(function () use ($trackingId) {
            return <<<HTML
<script async src="https://www.googletagmanager.com/gtag/js?id={$trackingId}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{$trackingId}');
</script>
HTML;
        });
    }
}

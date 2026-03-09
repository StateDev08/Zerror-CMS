<?php

namespace App\Providers;

use App\Widgets\CookieBannerWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class CookieBannerModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new CookieBannerWidget(), ['sidebar', 'home']);
    }
}

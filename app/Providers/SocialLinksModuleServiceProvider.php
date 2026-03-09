<?php

namespace App\Providers;

use App\Widgets\SocialLinksWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class SocialLinksModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new SocialLinksWidget(), ['sidebar', 'home']);
    }
}

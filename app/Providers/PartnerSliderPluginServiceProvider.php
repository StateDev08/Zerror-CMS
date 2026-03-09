<?php

namespace App\Providers;

use App\Widgets\PartnerSliderWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class PartnerSliderPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new PartnerSliderWidget(), ['sidebar', 'home']);
    }
}

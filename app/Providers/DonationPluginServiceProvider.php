<?php

namespace App\Providers;

use App\Widgets\DonationWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class DonationPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new DonationWidget(), ['sidebar', 'home']);
    }
}

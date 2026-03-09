<?php

namespace App\Providers;

use App\Widgets\TwitchWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class TwitchModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new TwitchWidget(), ['sidebar', 'home']);
    }
}

<?php

namespace App\Providers;

use App\Widgets\Ts3ViewerWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class Ts3ViewerModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new Ts3ViewerWidget(), ['sidebar', 'home']);
    }
}

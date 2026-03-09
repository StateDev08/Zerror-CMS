<?php

namespace App\Providers;

use App\Widgets\ServerStatusWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class ServerStatusModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new ServerStatusWidget(), ['sidebar', 'home']);
    }
}

<?php

namespace App\Providers;

use App\Widgets\TeamspeakWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class TeamspeakModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new TeamspeakWidget(), ['sidebar', 'home']);
    }
}

<?php

namespace App\Providers;

use App\Widgets\TwitchEmbedWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class TwitchEmbedPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new TwitchEmbedWidget(), ['sidebar', 'home']);
    }
}

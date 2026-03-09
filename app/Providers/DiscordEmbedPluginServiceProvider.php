<?php

namespace App\Providers;

use App\Widgets\DiscordEmbedWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class DiscordEmbedPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new DiscordEmbedWidget(), ['sidebar', 'home']);
    }
}

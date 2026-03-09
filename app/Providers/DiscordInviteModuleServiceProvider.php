<?php

namespace App\Providers;

use App\Widgets\DiscordInviteWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class DiscordInviteModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new DiscordInviteWidget(), ['sidebar', 'home']);
    }
}

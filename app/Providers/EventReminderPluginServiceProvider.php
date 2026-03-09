<?php

namespace App\Providers;

use App\Widgets\EventReminderWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class EventReminderPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new EventReminderWidget(), ['sidebar', 'home']);
    }
}

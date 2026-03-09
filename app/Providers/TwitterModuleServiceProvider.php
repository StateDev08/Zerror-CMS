<?php

namespace App\Providers;

use App\Widgets\TwitterWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class TwitterModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new TwitterWidget(), ['sidebar', 'home']);
    }
}

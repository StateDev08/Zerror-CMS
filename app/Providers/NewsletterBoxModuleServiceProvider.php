<?php

namespace App\Providers;

use App\Widgets\NewsletterBoxWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class NewsletterBoxModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new NewsletterBoxWidget(), ['sidebar', 'home']);
    }
}

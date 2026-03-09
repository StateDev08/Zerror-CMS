<?php

namespace App\Providers;

use App\Widgets\VoiceChatWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class VoiceChatModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register(new VoiceChatWidget(), ['sidebar', 'home']);
    }
}

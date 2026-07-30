<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

require_once __DIR__.'/Counter.php';

if (! class_exists('ZerroVisitorCounterModuleServiceProvider', false)) {
    class ZerroVisitorCounterModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/visitor_counter/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_visitor_counter');
                view()->addNamespace('mod_visitor_counter', $views);
            }

            app(PluginManager::class)->registerHeadHtml(function () {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }

                $href = e(asset('css/visitor-counter.css').'?v=1');

                return '<link rel="stylesheet" href="'.$href.'">';
            });
        }
    }
}

return ZerroVisitorCounterModuleServiceProvider::class;

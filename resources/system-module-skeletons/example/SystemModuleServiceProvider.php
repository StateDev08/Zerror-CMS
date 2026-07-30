<?php

use Illuminate\Support\ServiceProvider;

/**
 * Beispiel-System-Modul.
 *
 * Vertrag v1: Routes, Views und Config dürfen hier registriert werden.
 * Filament-ACP-Seiten aus Paketen: Phase 2.
 *
 * Wird von SystemModuleManager per require geladen.
 * Rückgabe: vollqualifizierter Provider-Klassenname.
 */
if (! class_exists('ZerroExampleSystemModuleServiceProvider', false)) {
    class ZerroExampleSystemModuleServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            // Bindings / Singletons bei Bedarf
        }

        public function boot(): void
        {
            $views = __DIR__.DIRECTORY_SEPARATOR.'views';
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'sysmod_example');
            }

            // Beispiel (auskommentiert):
            // Route::middleware('web')->group(function () {
            //     Route::get('/example-system', fn () => view('sysmod_example::index'))
            //         ->name('system_module.example');
            // });
        }
    }
}

return ZerroExampleSystemModuleServiceProvider::class;

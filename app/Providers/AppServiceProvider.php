<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\MarketplaceListing;
use App\Observers\EventObserver;
use App\Observers\MarketplaceListingObserver;
use App\Support\PluginManager;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use App\Support\ThemeManager;
use App\Support\ModuleManager;
use App\Support\WidgetRenderer;
use App\Widgets\LatestForumPostsWidget;
use App\Widgets\LatestNewsWidget;
use App\Widgets\UpcomingEventsWidget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        RedirectIfAuthenticated::redirectUsing(fn ($request) => route('usercp.index'));

        $this->app->singleton(WidgetRegistry::class, function () {
            $registry = new WidgetRegistry();
            $registry->register(new LatestNewsWidget(), ['sidebar', 'home']);
            $registry->register(new UpcomingEventsWidget(), ['sidebar', 'home']);
            $registry->register(new LatestForumPostsWidget(), ['sidebar', 'home']);
            return $registry;
        });

        $this->app->singleton(WidgetRenderer::class, function ($app) {
            return new WidgetRenderer($app->make(WidgetRegistry::class));
        });

        $this->app->singleton(ThemeManager::class);
        $this->app->singleton(PluginManager::class);
        $this->app->singleton(ModuleManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Asset- und Storage-URLs immer für die aktuelle Domain erzeugen (z. B. Subdomain
        // lichtbringer.drenor.de), damit keine CORS/404 durch falsche APP_URL (z. B. drenor.de) entstehen.
        if ($this->app->runningInConsole() === false && request()->hasHeader('Host')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(request()->getSchemeAndHttpHost());
            // Public-Disk-URLs über Laravel-Route ausliefern (umgeht 403/404 ohne Symlink).
            config(['filesystems.disks.public.url' => request()->getSchemeAndHttpHost() . '/app-storage']);
        }

        // Vor Installation: Sessions in Dateien, damit keine DB-Tabelle „sessions“ nötig ist
        if (! \App\Support\Installer::isInstalled()) {
            config(['session.driver' => 'file']);
        }

        // Rechte-System: super-admin darf alles, sonst Permission prüfen
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability, $arguments) {
            if (! $user || ! \Illuminate\Support\Facades\Request::is('admin*')) {
                return null;
            }
            if ($user->hasRole('super-admin')) {
                return true;
            }
            if ($ability === 'access_admin') {
                return $user->can('access_admin') ? true : null;
            }
            $model = $arguments[0] ?? null;
            $perm = \App\Support\PermissionHelper::abilityToPermissionName($ability, $model);
            if ($perm && $user->hasPermissionTo($perm)) {
                return true;
            }
            return null;
        });

        $themeManager = $this->app->make(ThemeManager::class);
        $themeManager->registerViewNamespace();

        if (! \App\Support\Installer::isInstalled()) {
            return;
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('plugins')) {
            $this->app->make(PluginManager::class)->bootEnabled();
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('modules')) {
            $this->app->make(ModuleManager::class)->loadEnabled();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('marketplace_listings')) {
            MarketplaceListing::observe(MarketplaceListingObserver::class);
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('events')) {
            Event::observe(EventObserver::class);
        }
    }
}

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
use App\Support\SystemModuleManager;
use App\Support\WidgetPackageManager;
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
            $registry->register(new LatestNewsWidget(), ['left', 'right']);
            $registry->register(new UpcomingEventsWidget(), ['left', 'right']);
            $registry->register(new LatestForumPostsWidget(), ['left', 'right']);
            return $registry;
        });

        $this->app->singleton(WidgetRenderer::class, function ($app) {
            return new WidgetRenderer($app->make(WidgetRegistry::class));
        });

        $this->app->singleton(ThemeManager::class);
        $this->app->singleton(PluginManager::class);
        $this->app->singleton(ModuleManager::class);
        $this->app->singleton(SystemModuleManager::class);
        $this->app->singleton(WidgetPackageManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ACP-Upload-Limits → Livewire + PHP (.user.ini), kein hardcodiertes max:10240
        try {
            \App\Support\UploadLimits::syncRuntimeLimits();
        } catch (\Throwable) {
            // Settings-DB ggf. noch nicht bereit
        }

        // Locale + Zeitzone aus Seiteneinstellungen (SSOT)
        try {
            if (\App\Support\Installer::isInstalled()
                && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $locale = trim((string) setting('app_locale', config('app.locale', 'de')));
                if (in_array($locale, ['de', 'en'], true)) {
                    \Illuminate\Support\Facades\App::setLocale($locale);
                    config(['app.locale' => $locale]);
                }
                $tz = site_timezone();
                config(['app.timezone' => $tz]);
                date_default_timezone_set($tz);
            }
        } catch (\Throwable) {
            // Settings-DB ggf. noch nicht bereit
        }

        // ACP-Tabellen: global Bearbeiten + Löschen (Resources ohne eigene recordActions)
        \Filament\Tables\Table::configureUsing(function (\Filament\Tables\Table $table): void {
            $table
                ->recordActions([
                    \Filament\Actions\EditAction::make()
                        ->visible(function ($livewire): bool {
                            if (! method_exists($livewire, 'getResource')) {
                                return false;
                            }

                            return $livewire::getResource()::hasPage('edit');
                        }),
                    \Filament\Actions\DeleteAction::make(),
                ])
                ->toolbarActions([
                    \Filament\Actions\BulkActionGroup::make([
                        \Filament\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        });

        // Passwort-Policy: min. 12 Zeichen, gemischt, nicht kompromittiert
        \Illuminate\Validation\Rules\Password::defaults(function () {
            $rule = \Illuminate\Validation\Rules\Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();

            return $this->app->environment('production') && \App\Support\Installer::isInstalled()
                ? $rule->uncompromised()
                : $rule;
        });

        // Asset- und Storage-URLs immer für die aktuelle Domain erzeugen (z. B. Subdomain
        // lichtbringer.drenor.de), damit keine CORS/404 durch falsche APP_URL (z. B. drenor.de) entstehen.
        if ($this->app->runningInConsole() === false && request()->hasHeader('Host')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(request()->getSchemeAndHttpHost());
            // Public-Disk-URLs über Laravel-Route ausliefern (umgeht 403/404 ohne Symlink).
            config(['filesystems.disks.public.url' => request()->getSchemeAndHttpHost() . '/app-storage']);
        }

        // Production: HTTPS nur erzwingen, wenn APP_URL wirklich https ist
        if ($this->app->environment('production') && ! $this->app->runningInConsole()) {
            $appUrl = (string) config('app.url');
            if (str_starts_with($appUrl, 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        // Vor Installation: Sessions in Dateien, damit keine DB-Tabelle „sessions“ nötig ist
        if (! \App\Support\Installer::isInstalled()) {
            config([
                'session.driver' => 'file',
                'session.encrypt' => false,
                'cache.default' => 'file',
            ]);
        }

        // Production: Session härten (Secure-Cookie nur bei HTTPS-APP_URL)
        // Encrypt erst nach Installation – sonst 500 ohne gültigen APP_KEY
        if ($this->app->environment('production') && \App\Support\Installer::isInstalled()) {
            $https = str_starts_with((string) config('app.url'), 'https://');
            config([
                'session.encrypt' => filter_var(env('SESSION_ENCRYPT', true), FILTER_VALIDATE_BOOLEAN),
                'session.secure' => env('SESSION_SECURE_COOKIE', $https),
                'session.http_only' => true,
                'session.same_site' => env('SESSION_SAME_SITE', 'lax'),
            ]);
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
                return $user->hasPermissionTo('access_admin') ? true : null;
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
        if (\Illuminate\Support\Facades\Schema::hasTable('system_modules')) {
            $this->app->make(SystemModuleManager::class)->loadEnabled();
        }

        $this->app->make(WidgetPackageManager::class)->loadAll();

        // Cookie-Banner: SSOT aus Settings (alle Themes via getBodyStartHtml)
        $this->app->make(PluginManager::class)->registerBodyStartHtml(function () {
            try {
                return view('partials.site-cookie-banner')->render();
            } catch (\Throwable $e) {
                return '';
            }
        });

        if (\Illuminate\Support\Facades\Schema::hasTable('marketplace_listings')) {
            MarketplaceListing::observe(MarketplaceListingObserver::class);
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('events')) {
            Event::observe(EventObserver::class);
        }
    }
}

<?php

namespace App\Providers\Filament;

use App\Http\Controllers\Admin\SiteMediaController;
use App\Support\ThemeManager;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $siteMode = app(ThemeManager::class)->getDefaultThemeMode();
        $filamentMode = match ($siteMode) {
            'dark' => ThemeMode::Dark,
            'light' => ThemeMode::Light,
            default => ThemeMode::System,
        };

        // Dark aus Site-Einstellung fest verankern (kein Zurücksetzen auf Weiß nach F5).
        // Nur bei "system" bleibt der Theme-Switcher frei.
        $darkEnabled = $siteMode !== 'light';

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode($darkEnabled, isForced: $siteMode === 'dark')
            ->defaultThemeMode($filamentMode)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->topNavigation()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function () use ($siteMode): string {
                    $mode = json_encode($siteMode);

                    return <<<HTML
<script>
(function () {
    var siteMode = {$mode};
    try {
        var stored = (localStorage.getItem('theme') || '').trim();
        if (siteMode === 'dark') {
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.add('dark');
        } else if (siteMode === 'light') {
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.remove('dark');
        } else if (!stored || (stored !== 'light' && stored !== 'dark' && stored !== 'system')) {
            localStorage.setItem('theme', 'system');
        }
    } catch (e) {}
})();
</script>
HTML;
                }
            )
            ->navigationGroups([
                'Inhalte',
                'Clan',
                'Community',
                'Markt & Jobs',
                'Benutzer',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\OnboardingChecklistWidget::class,
                \App\Filament\Widgets\ProductionHealthWidget::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="'.e(asset('css/zc-admin.css')).'?v=14">'
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('partials.cms-toasts')->render()
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.hooks.system-menu')->render()
            )
            ->authenticatedRoutes(function (): void {
                Route::post('site-media', [SiteMediaController::class, 'update'])->name('site-media');
            });
    }
}

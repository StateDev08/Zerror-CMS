<?php

namespace App\Providers;

use App\Support\PluginManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CookieConsentPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $config = plugin_config('cookie_consent');
        $bannerText = trim((string) ($config['banner_text'] ?? 'Wir nutzen Cookies. Mit der Nutzung stimmst du zu.'));
        $privacyUrl = trim((string) ($config['privacy_url'] ?? ''));
        $acceptButton = trim((string) ($config['accept_button'] ?? 'Akzeptieren'));
        if ($bannerText === '') {
            $bannerText = 'Wir nutzen Cookies. Mit der Nutzung stimmst du zu.';
        }
        app(PluginManager::class)->registerBodyStartHtml(function () use ($bannerText, $privacyUrl, $acceptButton) {
            return View::make('partials.cookie-consent', [
                'banner_text' => $bannerText,
                'privacy_url' => $privacyUrl,
                'accept_button' => $acceptButton,
            ])->render();
        });
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // JS-gesetzte Cookies (Klartext) – sonst verwirft EncryptCookies den Wert
        $middleware->encryptCookies(except: [
            'zerrocms_cookie_consent',
            'zerrocms_theme_mode',
            'zerrocms_theme_user_choice',
            'zerrocms_theme_mode_rev',
            'googtrans',
        ]);
        $middleware->web(prepend: [
            \App\Http\Middleware\EnsureEnvForInstall::class,
        ], append: [
            \App\Http\Middleware\LoadDbTranslations::class,
            \App\Http\Middleware\RedirectIfNotInstalled::class,
            \App\Http\Middleware\EnsureNotMaintenanceMode::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->alias([
            'install.redirect' => \App\Http\Middleware\RedirectIfInstalled::class,
            'discord.bot.api' => \App\Http\Middleware\ValidateDiscordBotApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

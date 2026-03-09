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
        $middleware->web(prepend: [
            \App\Http\Middleware\EnsureEnvForInstall::class,
        ], append: [
            \App\Http\Middleware\LoadDbTranslations::class,
            \App\Http\Middleware\RedirectIfNotInstalled::class,
            \App\Http\Middleware\EnsureNotMaintenanceMode::class,
        ]);
        $middleware->alias([
            'install.redirect' => \App\Http\Middleware\RedirectIfInstalled::class,
            'discord.bot.api' => \App\Http\Middleware\ValidateDiscordBotApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

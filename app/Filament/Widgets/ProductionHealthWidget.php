<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ProductionHealthWidget extends Widget
{
    protected string $view = 'filament.widgets.production-health';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super-admin') || $user->hasPermissionTo('access_admin'));
    }

    /**
     * @return list<array{level: string, text: string}>
     */
    public function getIssues(): array
    {
        $issues = [];

        if (config('app.debug')) {
            $issues[] = [
                'level' => 'danger',
                'text' => 'APP_DEBUG ist an. Vor Public-Launch: php artisan zerrocms:go-live --force',
            ];
        }

        if (config('app.env') !== 'production') {
            $issues[] = [
                'level' => 'warning',
                'text' => 'APP_ENV ist „'.config('app.env').'“, empfohlen: production.',
            ];
        }

        $mailer = config('mail.default');
        if (in_array($mailer, ['log', 'array'], true)) {
            $issues[] = [
                'level' => 'warning',
                'text' => 'E-Mail läuft über „'.$mailer.'“. Für Passwort-Reset/Newsletter SMTP in .env setzen.',
            ];
        }

        if (! file_exists(public_path('build/manifest.json'))) {
            $issues[] = [
                'level' => 'info',
                'text' => 'Kein Vite-Build gefunden – Tailwind-CDN-Fallback aktiv. Optional: npm run build',
            ];
        }

        return $issues;
    }
}

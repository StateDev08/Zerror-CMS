<?php

namespace App\Console\Commands;

use App\Support\Installer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GoLiveCommand extends Command
{
    protected $signature = 'zerrocms:go-live
                            {--force : .env ohne Rückfrage auf Production setzen}
                            {--check : Nur prüfen, nichts ändern}';

    protected $description = 'Macht ZerroCMS public-/production-ready (.env härten, Checks)';

    public function handle(): int
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            $this->error('.env nicht gefunden.');

            return self::FAILURE;
        }

        $checks = $this->runChecks();
        foreach ($checks as $label => $ok) {
            $this->line(($ok ? '[OK]  ' : '[!!] ').$label);
        }

        if ($this->option('check')) {
            return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Production-Einstellungen in .env setzen?', true)) {
            $this->warn('Abgebrochen.');

            return self::SUCCESS;
        }

        $envContent = File::get($envPath);
        $appUrl = 'http://localhost';
        if (preg_match('/^APP_URL=(.*)$/m', $envContent, $m)) {
            $appUrl = trim($m[1], " \t\"'");
        }
        $https = str_starts_with($appUrl, 'https://');

        $this->patchEnv([
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'LOG_LEVEL' => 'error',
            'SESSION_ENCRYPT' => 'true',
            'SESSION_SECURE_COOKIE' => $https ? 'true' : 'false',
        ]);

        $this->info('.env auf Production gesetzt.');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        $mailer = env('MAIL_MAILER', 'log');
        if ($mailer === 'log' || $mailer === 'array') {
            $this->warn('MAIL_MAILER ist noch "'.$mailer.'". Für Passwort-Reset/Newsletter bitte SMTP in .env setzen (Admin → Installer-Mail oder .env).');
        }

        if (! file_exists(public_path('build/manifest.json'))) {
            $this->warn('Kein Vite-Build (public/build/manifest.json). Optional: npm ci && npm run build');
        }

        $this->newLine();
        $this->info('Public-Launch-Basis ist gesetzt. Danach einmal Frontend + /admin durchklicken.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, bool>
     */
    protected function runChecks(): array
    {
        return [
            'Installation abgeschlossen' => Installer::isInstalled(),
            'APP_KEY gesetzt' => filled(config('app.key')),
            'APP_DEBUG aus (oder wird gesetzt)' => ! config('app.debug') || true,
            'storage/app/public erreichbar' => is_dir(storage_path('app/public')),
            'Themes vorhanden' => is_dir(base_path('themes')),
        ];
    }

    /**
     * @param  array<string, string>  $values
     */
    protected function patchEnv(array $values): void
    {
        $path = base_path('.env');
        $content = File::get($path);

        foreach ($values as $key => $value) {
            $line = $key.'='.$value;
            if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $content)) {
                $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $content) ?? $content;
            } else {
                $content = rtrim($content)."\n".$line."\n";
            }
        }

        File::put($path, $content);
    }
}

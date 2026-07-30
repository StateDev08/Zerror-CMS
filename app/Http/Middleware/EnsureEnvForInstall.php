<?php

namespace App\Http\Middleware;

use App\Support\Installer;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stellt .env/APP_KEY sicher und leitet vor der Installation auf /install um.
 * Läuft als erstes Web-Middleware – auch ohne manuelle Plesk-/SSH-Schritte.
 */
class EnsureEnvForInstall
{
    public function handle(Request $request, \Closure $next): Response
    {
        $this->ensureEnvFileAndKey();

        $isInstall = $request->is('install') || $request->is('install/*');

        if (! Installer::isInstalled() && ! $isInstall) {
            // Noch nicht installiert → immer in den Web-Installer
            return redirect()->to('/install')->withHeaders(['Cache-Control' => 'no-cache, no-store']);
        }

        if ($isInstall && ! $this->isAppKeyValid()) {
            $fixed = $this->tryFixAppKey();
            if ($fixed) {
                return redirect()->to($request->fullUrl())
                    ->withHeaders(['Cache-Control' => 'no-cache, no-store']);
            }

            return $this->appKeyInvalidResponse();
        }

        return $next($request);
    }

    private function ensureEnvFileAndKey(): void
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (! File::exists($envPath)) {
            try {
                if (File::exists($examplePath)) {
                    File::copy($examplePath, $envPath);
                } else {
                    File::put($envPath, "APP_KEY=\nSESSION_DRIVER=file\nSESSION_ENCRYPT=false\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n");
                }
            } catch (\Throwable $e) {
                // Preinstall/Bootstrap zeigt Anleitung
            }
        }

        if (File::exists($envPath) && ! $this->isAppKeyValid()) {
            $this->tryFixAppKey();
        }
    }

    private function isAppKeyValid(): bool
    {
        $keyString = config('app.key');
        if ($keyString === null || $keyString === '') {
            // Config ggf. noch leer – .env direkt lesen
            $envPath = base_path('.env');
            if (File::exists($envPath) && preg_match('/^\s*APP_KEY\s*=\s*(\S+)/m', File::get($envPath), $m)) {
                $keyString = trim($m[1], "\"'");
            }
        }
        if ($keyString === null || $keyString === '') {
            return false;
        }
        $rawKey = $keyString;
        if (Str::startsWith($keyString, 'base64:')) {
            $rawKey = base64_decode(Str::after($keyString, 'base64:'), true);
            if ($rawKey === false) {
                return false;
            }
        }
        $cipher = config('app.cipher') ?: 'AES-256-CBC';

        return Encrypter::supported($rawKey, $cipher);
    }

    private function tryFixAppKey(): bool
    {
        try {
            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('config:clear');

            return true;
        } catch (\Throwable $e) {
            // Fallback ohne Artisan
            try {
                $envPath = base_path('.env');
                if (! File::exists($envPath)) {
                    return false;
                }
                $key = 'base64:'.base64_encode(random_bytes(32));
                $content = File::get($envPath);
                if (preg_match('/^\s*APP_KEY\s*=/m', $content)) {
                    $content = preg_replace('/^\s*APP_KEY\s*=.*/m', 'APP_KEY='.$key, $content, 1);
                } else {
                    $content = rtrim($content)."\nAPP_KEY={$key}\n";
                }
                File::put($envPath, $content);
                config(['app.key' => $key]);

                return Encrypter::supported(base64_decode(substr($key, 7), true) ?: '', config('app.cipher') ?: 'AES-256-CBC');
            } catch (\Throwable $e2) {
                return false;
            }
        }
    }

    private function appKeyInvalidResponse(): Response
    {
        $htmlPath = resource_path('views/install/app-key-invalid.html');
        if (File::exists($htmlPath)) {
            return new Response(File::get($htmlPath), 200, ['Content-Type' => 'text/html; charset=utf-8']);
        }

        return new Response(
            '<!DOCTYPE html><html lang="de"><body style="font-family:system-ui;max-width:640px;margin:2rem auto;padding:0 1rem"><h1>APP_KEY fehlt</h1><p><a href="/preinstall.php">→ Vorbereitung starten</a></p></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=utf-8']
        );
    }
}

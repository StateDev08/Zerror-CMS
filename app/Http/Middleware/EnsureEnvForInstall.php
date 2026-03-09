<?php

namespace App\Http\Middleware;

use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Läuft nur bei /install*. Wenn .env fehlt: Versuch, sie aus .env.example zu erstellen.
 * Ist APP_KEY ungültig (fehlt oder falsche Länge für den Cipher): key:generate versuchen, sonst Anleitung anzeigen.
 * So wird kein gültiger APP_KEY benötigt, bevor Session/Encryption laufen.
 */
class EnsureEnvForInstall
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (! $request->is('install') && ! $request->is('install/*')) {
            return $next($request);
        }

        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            return $this->tryCreateEnvOrShowInstructions();
        }

        if (! $this->isAppKeyValid()) {
            $fixed = $this->tryFixAppKey();
            if ($fixed) {
                return redirect()->to($request->fullUrl())
                    ->withHeaders(['Cache-Control' => 'no-cache, no-store']);
            }
            return $this->appKeyInvalidResponse();
        }

        return $next($request);
    }

    private function tryCreateEnvOrShowInstructions(): Response
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');
        if (File::exists($examplePath)) {
            try {
                File::copy($examplePath, $envPath);
                Artisan::call('key:generate', ['--force' => true]);
                if (File::exists($envPath)) {
                    Artisan::call('config:clear');
                    return redirect()->to('/install')->withHeaders(['Cache-Control' => 'no-cache, no-store']);
                }
            } catch (\Throwable $e) {
                // Schreibrechte oder andere Fehler – Anleitung anzeigen
            }
        }
        return $this->envRequiredResponse();
    }

    private function isAppKeyValid(): bool
    {
        $keyString = config('app.key');
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
            return false;
        }
    }

    private function appKeyInvalidResponse(): Response
    {
        $htmlPath = resource_path('views/install/app-key-invalid.html');
        if (File::exists($htmlPath)) {
            return new Response(File::get($htmlPath), 200, ['Content-Type' => 'text/html; charset=utf-8']);
        }
        return new Response($this->appKeyInvalidFallbackHtml(), 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function appKeyInvalidFallbackHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZerroCMS – APP_KEY ungültig</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
        h1 { color: #0f172a; }
        code { background: #f1f5f9; padding: 0.2em 0.4em; border-radius: 4px; }
        a { color: #b45309; }
    </style>
</head>
<body>
    <h1>APP_KEY ungültig</h1>
    <p>Der Schlüssel in der Datei <strong>.env</strong> (APP_KEY) ist leer oder hat eine falsche Länge. Laravel benötigt einen gültigen Schlüssel im Format <code>base64:...</code> (z. B. für AES-256-CBC: 32 Bytes).</p>
    <p><strong>So beheben Sie das:</strong></p>
    <ol>
        <li>Per SSH im Projektordner: <code>php artisan key:generate</code> ausführen. Das überschreibt APP_KEY in .env mit einem gültigen Wert.</li>
        <li>Oder in .env die Zeile <code>APP_KEY=</code> leer lassen und <a href="/install">/install</a> erneut aufrufen – der Installer versucht dann, einen Schlüssel zu erzeugen (falls .env beschreibbar ist).</li>
    </ol>
    <p><a href="/install">→ Install-Seite erneut aufrufen</a></p>
</body>
</html>
HTML;
    }

    private function envRequiredResponse(): Response
    {
        $htmlPath = resource_path('views/install/env-required.html');
        if (File::exists($htmlPath)) {
            return new Response(File::get($htmlPath), 200, ['Content-Type' => 'text/html; charset=utf-8']);
        }
        return new Response($this->fallbackHtml(), 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function fallbackHtml(): string
    {
        $minEnv = $this->minimalEnvContent();
        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZerroCMS – .env anlegen</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
        h1 { color: #0f172a; }
        .box { background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; padding: 1rem; margin: 1rem 0; overflow-x: auto; }
        code { font-size: 0.85em; white-space: pre; }
        a { color: #b45309; }
    </style>
</head>
<body>
    <h1>ZerroCMS – .env manuell anlegen</h1>
    <p>Die Datei <strong>.env</strong> konnte nicht automatisch erstellt werden (z. B. fehlende Schreibrechte im Projektordner).</p>
    <p><strong>So beheben Sie das:</strong></p>
    <ol>
        <li>Im Projektordner (dort, wo <code>artisan</code> liegt) eine Datei <strong>.env</strong> anlegen.</li>
        <li>Entweder <strong>.env.example</strong> dorthin kopieren und als <strong>.env</strong> speichern.</li>
        <li>Oder den folgenden Mindestinhalt in .env eintragen und speichern:</li>
    </ol>
    <div class="box"><code>{$minEnv}</code></div>
    <p>Anschließend per SSH: <code>php artisan key:generate</code> ausführen, oder <a href="/install">/install</a> erneut aufrufen (der Installer erzeugt den Schlüssel dann).</p>
    <p><a href="/install">→ Install-Seite erneut aufrufen</a></p>
</body>
</html>
HTML;
    }

    private function minimalEnvContent(): string
    {
        return htmlspecialchars(
            "APP_NAME=ZerroCMS\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=https://meinedomain.de\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=zerrocms\nDB_USERNAME=root\nDB_PASSWORD=\n\nSESSION_DRIVER=file\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n\nCLAN_NAME=ZerroCMS\nCLAN_THEME=default",
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

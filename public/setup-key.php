<?php
/**
 * Erzeugt APP_KEY in .env, falls fehlend. Einmal aufrufen bei "MissingAppKeyException".
 * Aufruf: https://deine-domain.de/setup-key.php
 * Danach diese Datei löschen (Sicherheit).
 */
header('Content-Type: text/html; charset=utf-8');
$root = dirname(__DIR__);
$envPath = $root . DIRECTORY_SEPARATOR . '.env';

if (!file_exists($envPath)) {
    echo '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"><title>Setup</title></head><body>';
    echo '<p>Datei <code>.env</code> fehlt. Bitte zuerst <code>.env.example</code> nach <code>.env</code> kopieren, dann diese Seite erneut aufrufen.</p>';
    echo '</body></html>';
    exit;
}

$content = file_get_contents($envPath);
$hasKey = (bool) preg_match('/^\s*APP_KEY\s*=\s*[^\s\n]/m', $content);

if ($hasKey) {
    header('Location: /install', true, 302);
    exit;
}

$key = 'base64:' . base64_encode(random_bytes(32));
if (preg_match('/^\s*APP_KEY\s*=\s*$/m', $content)) {
    $content = preg_replace('/^(\s*APP_KEY\s*)=\s*$/m', '$1=' . $key, $content, 1);
} elseif (preg_match('/^\s*APP_KEY\s*=\s*.*$/m', $content)) {
    $content = preg_replace('/^(\s*APP_KEY\s*)=\s*.*/m', '$1=' . $key, $content, 1);
} else {
    $content = trim($content) . "\nAPP_KEY=" . $key . "\n";
}

if (file_put_contents($envPath, $content) === false) {
    echo '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"><title>Fehler</title></head><body>';
    echo '<p>Konnte .env nicht schreiben (Schreibrechte?). Bitte APP_KEY per SSH setzen: <code>php artisan key:generate</code></p>';
    echo '</body></html>';
    exit;
}

header('Location: /install', true, 302);
exit;

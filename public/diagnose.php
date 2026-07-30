<?php
/**
 * Schnelldiagnose ohne Laravel-Boot.
 * Aufruf: https://deine-domain.de/diagnose.php — danach löschen.
 */
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__.'/includes/ZerroEnvBootstrap.php';

$root = ZerroEnvBootstrap::root();
$boot = ZerroEnvBootstrap::ensure();
$checks = [];

$checks[] = [
    'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'label' => 'PHP-Version',
    'detail' => PHP_VERSION,
];
$checks[] = [
    'ok' => ZerroEnvBootstrap::vendorReady(),
    'label' => 'Composer vendor/',
    'detail' => ZerroEnvBootstrap::vendorReady() ? 'vorhanden' : 'fehlt – /preinstall.php nutzen',
];
$checks[] = [
    'ok' => is_file($root.DIRECTORY_SEPARATOR.'.env'),
    'label' => '.env',
    'detail' => is_file($root.DIRECTORY_SEPARATOR.'.env') ? 'vorhanden' : 'fehlt',
];
$checks[] = [
    'ok' => ZerroEnvBootstrap::canRunComposer(),
    'label' => 'proc_open (Composer)',
    'detail' => ZerroEnvBootstrap::canRunComposer() ? 'erlaubt' : 'blockiert – vendor/ im ZIP mitliefern',
];
$checks[] = [
    'ok' => ZerroEnvBootstrap::canDownload() || is_file($root.DIRECTORY_SEPARATOR.'composer.phar'),
    'label' => 'composer.phar Download',
    'detail' => ZerroEnvBootstrap::canDownload() || is_file($root.DIRECTORY_SEPARATOR.'composer.phar') ? 'OK' : 'nicht möglich',
];

$requiredExt = ['mbstring', 'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'bcmath'];
$missing = array_values(array_filter($requiredExt, fn ($e) => ! extension_loaded($e)));
$checks[] = [
    'ok' => $missing === [],
    'label' => 'PHP-Extensions',
    'detail' => $missing === [] ? 'alle vorhanden' : ('fehlen: '.implode(', ', $missing)),
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZerroCMS Diagnose</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
        .ok { color: #15803d; font-weight: 600; } .bad { color: #b91c1c; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; } td, th { text-align: left; padding: .45rem; border-bottom: 1px solid #e2e8f0; }
        .btn { display: inline-block; margin-top: 1rem; background: #d97706; color: #111; font-weight: 700; text-decoration: none; padding: .7rem 1rem; border-radius: 8px; }
    </style>
</head>
<body>
<h1>ZerroCMS Diagnose</h1>
<p><?= htmlspecialchars(implode(' · ', $boot['messages'] ?: ['Bootstrap OK'])) ?></p>
<table>
    <thead><tr><th>Check</th><th>Status</th><th>Detail</th></tr></thead>
    <tbody>
    <?php foreach ($checks as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['label']) ?></td>
            <td class="<?= $c['ok'] ? 'ok' : 'bad' ?>"><?= $c['ok'] ? 'OK' : 'FEHLER' ?></td>
            <td><?= htmlspecialchars($c['detail']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p>
    <a class="btn" href="/preinstall.php">Vorbereitung / Composer</a>
    <a class="btn" href="/install" style="background:#0f172a;color:#fff;margin-left:.5rem">Installer</a>
</p>
<p style="color:#b45309;font-size:.9rem">diagnose.php nach dem Setup löschen.</p>
</body>
</html>

<?php
/**
 * Web-Preinstall ohne SSH: .env, APP_KEY, Composer-Download, vendor/.
 * Wird von index.php genutzt, wenn vendor fehlt, und ist auch direkt aufrufbar.
 */
declare(strict_types=1);

@ini_set('display_errors', '0');
@ini_set('max_execution_time', '0');
@set_time_limit(0);
@ignore_user_abort(true);

require_once __DIR__.'/includes/ZerroEnvBootstrap.php';

$root = ZerroEnvBootstrap::root();
$bootstrap = ZerroEnvBootstrap::ensure();
$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$log = [];
$error = null;
$success = null;

if ($isPost && $action === 'install_vendor') {
    if (ZerroEnvBootstrap::vendorReady()) {
        $success = 'vendor/ ist bereits vorhanden.';
    } elseif (! ZerroEnvBootstrap::canRunComposer()) {
        $error = 'Dieser Hoster blockiert proc_open. Bitte das CMS-ZIP inkl. fertigem Ordner vendor/ per Dateimanager hochladen – danach diese Seite neu laden.';
    } else {
        $phar = $root.DIRECTORY_SEPARATOR.'composer.phar';
        if (! is_file($phar)) {
            if (! ZerroEnvBootstrap::canDownload()) {
                $error = 'composer.phar fehlt und Download ist deaktiviert (curl/allow_url_fopen).';
            } else {
                $dl = zerro_download_composer_phar($phar);
                if ($dl !== true) {
                    $error = 'composer.phar Download fehlgeschlagen: '.$dl;
                } else {
                    $log[] = 'composer.phar heruntergeladen';
                }
            }
        }

        if ($error === null) {
            $result = zerro_run_composer_install($root);
            if ($result['ok']) {
                $success = 'Composer install erfolgreich. Weiter zum Installer…';
                $log = array_merge($log, $result['log']);
            } else {
                $error = $result['error'];
                $log = array_merge($log, $result['log']);
            }
        }
    }
}

if (ZerroEnvBootstrap::vendorReady() && ($action === 'continue' || $success !== null)) {
    header('Location: /install', true, 302);
    exit;
}

$vendorOk = ZerroEnvBootstrap::vendorReady();
$canComposer = ZerroEnvBootstrap::canRunComposer();
$canDownload = ZerroEnvBootstrap::canDownload();
$hasPhar = is_file($root.DIRECTORY_SEPARATOR.'composer.phar');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZerroCMS – Vorbereitung</title>
    <style>
        :root { --bg:#0f172a; --card:#1e293b; --text:#e2e8f0; --muted:#94a3b8; --ok:#4ade80; --bad:#f87171; --accent:#f59e0b; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, sans-serif; background: linear-gradient(160deg,#0f172a,#1e293b 40%,#0f172a); color: var(--text); min-height:100vh; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 2.5rem 1.25rem; }
        h1 { font-size: 1.6rem; margin: 0 0 .5rem; }
        p { color: var(--muted); line-height: 1.55; }
        .card { background: rgba(30,41,59,.9); border:1px solid #334155; border-radius: 14px; padding: 1.25rem; margin-top: 1.25rem; }
        .ok { color: var(--ok); } .bad { color: var(--bad); }
        ul { padding-left: 1.1rem; margin: .75rem 0; }
        li { margin: .35rem 0; }
        button, .btn {
            display:inline-block; border:0; cursor:pointer; text-decoration:none;
            background: var(--accent); color:#111; font-weight:700; padding:.75rem 1.1rem;
            border-radius: 10px; font-size: .95rem;
        }
        button:disabled { opacity:.5; cursor:not-allowed; }
        .muted { font-size:.875rem; color: var(--muted); }
        pre { background:#0b1220; color:#cbd5e1; padding:.9rem; border-radius:10px; overflow:auto; font-size:.8rem; max-height:240px; }
        .alert { padding:.85rem 1rem; border-radius:10px; margin-top:1rem; }
        .alert-ok { background: rgba(74,222,128,.12); border:1px solid rgba(74,222,128,.35); color:#bbf7d0; }
        .alert-bad { background: rgba(248,113,113,.12); border:1px solid rgba(248,113,113,.35); color:#fecaca; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>ZerroCMS Vorbereitung</h1>
    <p>Ohne SSH: Umgebung und Abhängigkeiten werden hier im Browser eingerichtet.</p>

    <div class="card">
        <strong>Status</strong>
        <ul>
            <li class="<?= $bootstrap['ok'] ? 'ok' : 'bad' ?>"><?= $bootstrap['ok'] ? '✓' : '✗' ?> .env / APP_KEY</li>
            <?php foreach ($bootstrap['messages'] as $m): ?>
                <li class="muted"><?= htmlspecialchars($m) ?></li>
            <?php endforeach; ?>
            <li class="<?= $vendorOk ? 'ok' : 'bad' ?>"><?= $vendorOk ? '✓' : '✗' ?> vendor/ (Composer-Pakete)</li>
            <li class="<?= $canComposer ? 'ok' : 'bad' ?>"><?= $canComposer ? '✓' : '✗' ?> PHP darf Prozesse starten (proc_open)</li>
            <li class="<?= ($canDownload || $hasPhar) ? 'ok' : 'bad' ?>"><?= ($canDownload || $hasPhar) ? '✓' : '✗' ?> composer.phar (vorhanden oder Download möglich)</li>
        </ul>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-bad"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($log): ?>
        <div class="card"><pre><?= htmlspecialchars(implode("\n", $log)) ?></pre></div>
    <?php endif; ?>

    <div class="card">
        <?php if ($vendorOk): ?>
            <p class="ok">Alles bereit für den Installer.</p>
            <p><a class="btn" href="/install">Weiter zur Installation →</a></p>
        <?php elseif ($canComposer && ($canDownload || $hasPhar)): ?>
            <p>Als Nächstes werden die PHP-Pakete installiert. Das kann einige Minuten dauern – Fenster offen lassen.</p>
            <form method="post" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Bitte warten…';">
                <input type="hidden" name="action" value="install_vendor">
                <button type="submit">Abhängigkeiten jetzt installieren</button>
            </form>
            <p class="muted" style="margin-top:1rem">Falls der Timeout greift: Button erneut klicken. Teilfortschritt bleibt erhalten.</p>
        <?php else: ?>
            <p class="bad">Automatische Composer-Installation auf diesem Hosting nicht möglich.</p>
            <p>Lade das CMS erneut hoch – diesmal <strong>inklusive Ordner <code>vendor/</code></strong> (vollständiges Release-ZIP). Danach diese Seite neu laden.</p>
            <p><a class="btn" href="/preinstall.php">Erneut prüfen</a></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php

function zerro_download_composer_phar(string $target): true|string
{
    $url = 'https://getcomposer.org/download/latest-stable/composer.phar';
    $data = null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => 'ZerroCMS-Installer',
        ]);
        $data = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($data === false || $code >= 400) {
            return $err !== '' ? $err : "HTTP {$code}";
        }
    } else {
        $ctx = stream_context_create(['http' => ['timeout' => 120, 'header' => "User-Agent: ZerroCMS-Installer\r\n"]]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data === false) {
            return 'file_get_contents fehlgeschlagen';
        }
    }

    if (strlen($data) < 100000) {
        return 'Download zu klein / ungültig';
    }
    if (@file_put_contents($target, $data) === false) {
        return 'Schreibrechte für composer.phar fehlen';
    }

    return true;
}

/**
 * @return array{ok:bool,error:?string,log:list<string>}
 */
function zerro_run_composer_install(string $root): array
{
    $log = [];
    $php = PHP_BINARY ?: 'php';
    $phar = $root.DIRECTORY_SEPARATOR.'composer.phar';
    if (! is_file($phar)) {
        return ['ok' => false, 'error' => 'composer.phar fehlt', 'log' => $log];
    }

    $cmd = escapeshellarg($php).' '.escapeshellarg($phar).' install --no-dev --no-interaction --prefer-dist --optimize-autoloader';
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $env = $_ENV + $_SERVER;
    $env['COMPOSER_HOME'] = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'composer';
    if (! is_dir($env['COMPOSER_HOME'])) {
        @mkdir($env['COMPOSER_HOME'], 0755, true);
    }

    $process = @proc_open($cmd, $descriptors, $pipes, $root, $env);
    if (! is_resource($process)) {
        return ['ok' => false, 'error' => 'proc_open fehlgeschlagen', 'log' => $log];
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($process);

    $combined = trim($stdout."\n".$stderr);
    if ($combined !== '') {
        $log[] = $combined;
    }

    if ($code !== 0 || ! is_file($root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php')) {
        return [
            'ok' => false,
            'error' => 'composer install fehlgeschlagen (Exit '.$code.'). Details siehe Log.',
            'log' => $log,
        ];
    }

    return ['ok' => true, 'error' => null, 'log' => $log];
}

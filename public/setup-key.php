<?php
/**
 * Legacy-URL: leitet auf den Web-Preinstall weiter (.env + Key inkl.).
 * Aufruf: https://deine-domain.de/setup-key.php
 */
require_once __DIR__.'/includes/ZerroEnvBootstrap.php';
ZerroEnvBootstrap::ensure();

if (! ZerroEnvBootstrap::vendorReady()) {
    header('Location: /preinstall.php', true, 302);
    exit;
}

header('Location: /install', true, 302);
exit;

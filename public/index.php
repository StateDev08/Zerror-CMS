<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Ohne SSH: .env + APP_KEY + storage-Ordner automatisch anlegen
require_once __DIR__.'/includes/ZerroEnvBootstrap.php';
ZerroEnvBootstrap::ensure();

$autoload = __DIR__.'/../vendor/autoload.php';
if (! is_file($autoload)) {
    // Kein Laravel möglich → Web-Preinstall (Composer im Browser)
    require __DIR__.'/preinstall.php';
    exit;
}

// Register the Composer autoloader...
require $autoload;

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

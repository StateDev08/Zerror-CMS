<?php

namespace App\Support;

class Installer
{
    /**
     * Pflicht-Extensions für Laravel 12 + Filament + ZerroCMS.
     *
     * @return list<string>
     */
    public static function requiredExtensions(): array
    {
        return [
            'pdo',
            'pdo_mysql',
            'mbstring',
            'openssl',
            'tokenizer',
            'xml',
            'ctype',
            'json',
            'bcmath',
            'fileinfo',
            'intl',
            'curl',
            'gd',
            'zip',
            'iconv',
        ];
    }

    /**
     * Empfohlene Extensions (Warnung, Installation trotzdem möglich).
     *
     * @return list<string>
     */
    public static function recommendedExtensions(): array
    {
        return [
            'exif',
            'sodium',
            'pcntl',
        ];
    }

    public static function isInstalled(): bool
    {
        $file = storage_path('installed');

        return file_exists($file) && trim((string) @file_get_contents($file)) === '1';
    }

    public static function markInstalled(): bool
    {
        $dir = storage_path();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir.'/installed';
        $written = @file_put_contents($path, '1');

        return $written !== false;
    }

    public static function clearInstalled(): void
    {
        $path = storage_path('installed');
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * @return array{
     *   ok: bool,
     *   php: bool,
     *   php_version: string,
     *   php_sapi: string,
     *   php_ini: string|false,
     *   extension_dir: string,
     *   extensions: array<string>,
     *   extension_status: array<string, bool>,
     *   recommended_missing: array<string>,
     *   writable: array<string>,
     *   writable_status: array<string, bool>,
     *   errors: array<string>,
     *   optional: array<string>,
     *   composer_available: bool,
     *   vendor_ok: bool,
     *   mysql_driver: bool
     * }
     */
    public static function checkRequirements(): array
    {
        $errors = [];
        $optional = [];
        $required = self::requiredExtensions();
        $extensionStatus = [];
        $missing = [];

        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            $extensionStatus[$ext] = $loaded;
            if (! $loaded) {
                $missing[] = $ext;
            }
        }

        $recommendedMissing = [];
        foreach (self::recommendedExtensions() as $ext) {
            // pcntl existiert unter Windows nicht – nicht als Warnung zählen
            if ($ext === 'pcntl' && PHP_OS_FAMILY === 'Windows') {
                continue;
            }
            if (! extension_loaded($ext)) {
                $recommendedMissing[] = $ext;
            }
        }

        $phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
        if (! $phpOk) {
            $errors[] = __('install.php_required', ['version' => PHP_VERSION]);
        }

        foreach ($missing as $ext) {
            $hint = self::extensionHint($ext);
            $errors[] = __('install.extension_missing', ['name' => $ext]).($hint !== '' ? ' '.$hint : '');
        }

        $writable = [
            'storage' => storage_path(),
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];
        $writableStatus = [];
        $notWritable = [];
        foreach ($writable as $label => $path) {
            if (! is_dir($path)) {
                @mkdir($path, 0755, true);
            }
            $ok = is_dir($path) && is_writable($path);
            $writableStatus[$label] = $ok;
            if (! $ok) {
                $notWritable[] = $label;
            }
        }
        foreach ($notWritable as $label) {
            $errors[] = __('install.not_writable', ['name' => $label]);
        }

        $vendorOk = is_file(base_path('vendor/autoload.php'));
        if (! $vendorOk) {
            $errors[] = __('install.vendor_missing');
        }

        $mysqlDriver = extension_loaded('pdo_mysql');
        if (! $mysqlDriver) {
            // bereits in missing, aber klarer Hinweis
            $errors[] = __('install.mysql_driver_missing');
        }

        // Apache + intl: oft ICU-DLLs nicht im Service-PATH
        if (PHP_SAPI === 'apache2handler' && in_array('intl', $missing, true)) {
            $errors[] = __('install.intl_apache_hint');
        }

        $composerAvailable = self::commandAvailable('composer')
            || is_file(base_path('composer.phar'))
            || function_exists('curl_init')
            || filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN);
        if ($composerAvailable) {
            $optional[] = __('install.optional_composer_ok');
        } else {
            $optional[] = __('install.optional_composer_missing');
        }

        if (self::commandAvailable('npm')) {
            $optional[] = __('install.optional_npm_ok');
        } else {
            $optional[] = __('install.optional_npm_missing');
        }

        foreach ($recommendedMissing as $ext) {
            $optional[] = __('install.recommended_missing', ['name' => $ext]);
        }

        $phpIni = php_ini_loaded_file();
        $extDir = (string) ini_get('extension_dir');

        return [
            'ok' => empty($errors),
            'php' => $phpOk,
            'php_version' => PHP_VERSION,
            'php_sapi' => PHP_SAPI,
            'php_ini' => $phpIni,
            'extension_dir' => $extDir,
            'extensions' => $missing,
            'extension_status' => $extensionStatus,
            'recommended_missing' => $recommendedMissing,
            'writable' => $notWritable,
            'writable_status' => $writableStatus,
            'errors' => array_values(array_unique($errors)),
            'optional' => $optional,
            'composer_available' => $composerAvailable,
            'vendor_ok' => $vendorOk,
            'mysql_driver' => $mysqlDriver,
        ];
    }

    protected static function extensionHint(string $ext): string
    {
        return match ($ext) {
            'intl' => __('install.hint_intl'),
            'gd' => __('install.hint_gd'),
            'zip' => __('install.hint_zip'),
            'curl' => __('install.hint_curl'),
            'pdo_mysql' => __('install.hint_pdo_mysql'),
            default => '',
        };
    }

    protected static function commandAvailable(string $command): bool
    {
        $out = [];
        $code = 1;
        if (PHP_OS_FAMILY === 'Windows') {
            @exec('where '.escapeshellarg($command).' 2>&1', $out, $code);
        } else {
            @exec('command -v '.escapeshellarg($command).' 2>/dev/null', $out, $code);
        }

        return $code === 0;
    }
}

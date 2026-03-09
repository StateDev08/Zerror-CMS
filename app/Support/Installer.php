<?php

namespace App\Support;

class Installer
{
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
        $path = $dir . '/installed';
        // Check write success so caller can show error (e.g. storage permissions)
        $written = @file_put_contents($path, '1');
        return $written !== false;
    }

    /**
     * @return array{ok: bool, php: bool, extensions: array<string>, writable: array<string>, errors: array<string>}
     */
    public static function checkRequirements(): array
    {
        $errors = [];
        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
        $missing = [];
        foreach ($extensions as $ext) {
            if (! extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $errors[] = __('install.php_required', ['version' => PHP_VERSION]);
        }
        foreach ($missing as $ext) {
            $errors[] = __('install.extension_missing', ['name' => $ext]);
        }
        $writable = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];
        $notWritable = [];
        foreach ($writable as $label => $path) {
            if (! is_dir($path)) {
                @mkdir($path, 0755, true);
            }
            if (! is_writable($path)) {
                $notWritable[] = $label;
            }
        }
        foreach ($notWritable as $label) {
            $errors[] = __('install.not_writable', ['name' => $label]);
        }
        return [
            'ok' => empty($errors),
            'php' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'extensions' => $missing,
            'writable' => $notWritable,
            'errors' => $errors,
        ];
    }
}

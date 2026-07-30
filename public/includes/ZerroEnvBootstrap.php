<?php

/**
 * Früher Bootstrap ohne Laravel/Composer.
 * Legt .env an, setzt APP_KEY, bereitet storage-Ordner vor.
 */
final class ZerroEnvBootstrap
{
    public static function root(): string
    {
        // Datei liegt in public/includes/ → zwei Ebenen hoch = Projektroot
        return dirname(__DIR__, 2);
    }

    public static function ensure(): array
    {
        $root = self::root();
        $messages = [];
        $ok = true;

        self::ensureDirectories($root, $messages);

        $envPath = $root.DIRECTORY_SEPARATOR.'.env';
        $examplePath = $root.DIRECTORY_SEPARATOR.'.env.example';

        if (! is_file($envPath)) {
            if (is_file($examplePath)) {
                if (@copy($examplePath, $envPath)) {
                    $messages[] = '.env aus .env.example erstellt';
                    self::patchEnvForWebInstall($envPath);
                } else {
                    $ok = false;
                    $messages[] = '.env konnte nicht erstellt werden (Schreibrechte im Projektroot?)';
                }
            } else {
                if (self::writeMinimalEnv($envPath)) {
                    $messages[] = 'Minimale .env erstellt';
                } else {
                    $ok = false;
                    $messages[] = '.env und .env.example fehlen – Schreibrechte prüfen';
                }
            }
        }

        if (is_file($envPath) && ! self::hasValidAppKey($envPath)) {
            if (self::writeAppKey($envPath)) {
                $messages[] = 'APP_KEY gesetzt';
            } else {
                $ok = false;
                $messages[] = 'APP_KEY konnte nicht geschrieben werden';
            }
        }

        return ['ok' => $ok, 'messages' => $messages, 'env' => is_file($envPath)];
    }

    public static function vendorReady(): bool
    {
        return is_file(self::root().DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
    }

    public static function canRunComposer(): bool
    {
        if (! function_exists('proc_open') || ! function_exists('proc_close')) {
            return false;
        }
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('proc_open', $disabled, true) || in_array('proc_close', $disabled, true)) {
            return false;
        }

        return true;
    }

    public static function canDownload(): bool
    {
        return function_exists('curl_init') || filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN);
    }

    private static function ensureDirectories(string $root, array &$messages): void
    {
        $dirs = [
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'bootstrap/cache',
        ];
        foreach ($dirs as $rel) {
            $path = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $rel);
            if (! is_dir($path)) {
                if (@mkdir($path, 0755, true)) {
                    $messages[] = "Ordner {$rel} angelegt";
                }
            }
        }
    }

    private static function patchEnvForWebInstall(string $envPath): void
    {
        $content = (string) file_get_contents($envPath);
        $patches = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'SESSION_DRIVER' => 'file',
            'SESSION_ENCRYPT' => 'false',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'sync',
            'LOG_LEVEL' => 'error',
            'HASH_VERIFY' => 'false',
        ];
        foreach ($patches as $key => $value) {
            if (preg_match('/^'.preg_quote($key, '/').'=/m', $content)) {
                $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $key.'='.$value, $content, 1);
            } else {
                $content = rtrim($content)."\n{$key}={$value}\n";
            }
        }
        @file_put_contents($envPath, $content);
    }

    private static function writeMinimalEnv(string $envPath): bool
    {
        $body = <<<'ENV'
APP_NAME=ZerroCMS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

APP_LOCALE=de
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zerrocms
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
LOG_LEVEL=error

HASH_DRIVER=argon2id
HASH_VERIFY=false

CLAN_NAME=ZerroCMS
CLAN_THEME=pax-dei
ENV;

        return @file_put_contents($envPath, $body) !== false;
    }

    private static function hasValidAppKey(string $envPath): bool
    {
        $content = (string) file_get_contents($envPath);
        if (! preg_match('/^\s*APP_KEY\s*=\s*(.+)$/m', $content, $m)) {
            return false;
        }
        $value = trim($m[1], " \t\"'");
        if ($value === '' || $value === 'null') {
            return false;
        }
        if (str_starts_with($value, 'base64:')) {
            $raw = base64_decode(substr($value, 7), true);

            return is_string($raw) && strlen($raw) === 32;
        }

        return strlen($value) >= 16;
    }

    private static function writeAppKey(string $envPath): bool
    {
        try {
            $key = 'base64:'.base64_encode(random_bytes(32));
        } catch (Throwable $e) {
            return false;
        }

        $content = (string) file_get_contents($envPath);
        if (preg_match('/^\s*APP_KEY\s*=/m', $content)) {
            $content = preg_replace('/^\s*APP_KEY\s*=.*/m', 'APP_KEY='.$key, $content, 1);
        } else {
            $content = rtrim($content)."\nAPP_KEY={$key}\n";
        }

        return @file_put_contents($envPath, $content) !== false;
    }
}

<?php

namespace App\Support;

/**
 * Globale Upload-Limits aus den ACP-Einstellungen.
 * Werte in MB in der DB; Laravel/Filament erwarten KB.
 */
class UploadLimits
{
    public const DEFAULT_IMAGE_MB = 10;

    public const DEFAULT_FILE_MB = 50;

    public const MIN_MB = 1;

    public const MAX_MB = 512;

    public static function imageMb(): int
    {
        return self::clamp((int) setting('upload_max_image_mb', self::DEFAULT_IMAGE_MB));
    }

    public static function fileMb(): int
    {
        return self::clamp((int) setting('upload_max_file_mb', self::DEFAULT_FILE_MB));
    }

    /** Filament FileUpload::maxSize() / Laravel max: – in Kilobyte */
    public static function imageKb(): int
    {
        return self::imageMb() * 1024;
    }

    public static function fileKb(): int
    {
        return self::fileMb() * 1024;
    }

    public static function imageRule(): string
    {
        return 'max:'.self::imageKb();
    }

    public static function fileRule(): string
    {
        return 'max:'.self::fileKb();
    }

    public static function imageMaxMessage(string $fieldLabel = 'Datei'): string
    {
        return __('settings.upload_max_error', [
            'label' => $fieldLabel,
            'mb' => self::imageMb(),
        ]);
    }

    public static function fileMaxMessage(string $fieldLabel = 'Datei'): string
    {
        return __('settings.upload_max_error', [
            'label' => $fieldLabel,
            'mb' => self::fileMb(),
        ]);
    }

    /** Laravel/Livewire `max:` Regel in Kilobyte (größeres aus Bild/Datei-ACP). */
    public static function livewireMaxKb(): int
    {
        return max(self::imageKb(), self::fileKb());
    }

    /** Livewire-Temp-Upload-Rules aus ACP (kein hartes 10-MB-Default). */
    public static function applyToLivewireConfig(): void
    {
        config([
            'livewire.temporary_file_upload.rules' => [
                'required',
                'file',
                'max:'.self::livewireMaxKb(),
            ],
        ]);
    }

    /**
     * Schreibt public_path('.user.ini') mit ACP-Limits (Plesk/PHP-FPM).
     * post_max_size etwas größer als upload_max_filesize.
     */
    public static function syncPhpUserIni(): bool
    {
        $uploadMb = (int) max(self::imageMb(), self::fileMb());
        $postMb = max($uploadMb + 10, (int) ceil($uploadMb * 1.1));

        $path = public_path('.user.ini');
        $contents = implode("\n", [
            '; Generiert aus ACP Upload-Limits – nicht manuell hardcoden',
            'upload_max_filesize = '.$uploadMb.'M',
            'post_max_size = '.$postMb.'M',
            '',
        ]);

        $ok = @file_put_contents($path, $contents) !== false;
        if ($ok) {
            @chmod($path, 0644);
        }

        return $ok;
    }

    /** Livewire + .user.ini an ACP anbinden */
    public static function syncRuntimeLimits(): void
    {
        self::applyToLivewireConfig();
        self::syncPhpUserIni();
    }

    /** PHP-Limit in MB (upload_max_filesize), falls parsebar */
    public static function phpUploadMb(): ?int
    {
        $raw = ini_get('upload_max_filesize');
        if (! is_string($raw) || $raw === '') {
            return null;
        }
        $raw = trim($raw);
        $unit = strtoupper(substr($raw, -1));
        $num = (float) $raw;
        $mb = match ($unit) {
            'G' => (int) round($num * 1024),
            'M' => (int) round($num),
            'K' => max(1, (int) ceil($num / 1024)),
            default => max(1, (int) ceil(((float) $raw) / 1024 / 1024)),
        };

        return max(1, $mb);
    }

    public static function clamp(int $mb): int
    {
        return max(self::MIN_MB, min(self::MAX_MB, $mb));
    }
}

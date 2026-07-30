<?php

namespace App\Filament\Forms;

use App\Support\UploadLimits;
use Filament\Forms\Components\FileUpload;

/**
 * Einheitliche File-Uploads – Limits immer aus ACP (Einstellungen → Uploads).
 */
class CmsFileUpload
{
    /** @return list<string> */
    public static function imageTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    }

    /** @return list<string> */
    public static function documentTypes(): array
    {
        return [
            'application/pdf',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/x-zip-compressed',
            'text/plain',
            'text/csv',
        ];
    }

    public static function image(string $name, string $directory, ?int $maxKb = null): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->acceptedFileTypes(self::imageTypes())
            ->maxSize(fn () => $maxKb ?? UploadLimits::imageKb())
            ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => UploadLimits::imageMb()]))
            ->imageEditor()
            ->visibility('public');
    }

    public static function media(string $name, string $directory = 'media', ?int $maxKb = null): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory($directory)
            ->acceptedFileTypes([
                ...self::imageTypes(),
                'application/pdf',
                'video/mp4',
                'audio/mpeg',
            ])
            ->maxSize(fn () => $maxKb ?? UploadLimits::fileKb())
            ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => UploadLimits::fileMb()]))
            ->visibility('public');
    }

    public static function audio(string $name, string $directory = 'music', ?int $maxKb = null): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory($directory)
            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
            ->maxSize(fn () => $maxKb ?? UploadLimits::fileKb())
            ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => UploadLimits::fileMb()]))
            ->visibility('public');
    }

    public static function download(string $name, string $directory = 'downloads', ?int $maxKb = null): FileUpload
    {
        return FileUpload::make($name)
            ->disk('public')
            ->directory($directory)
            ->acceptedFileTypes(self::documentTypes())
            ->maxSize(fn () => $maxKb ?? UploadLimits::fileKb())
            ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => UploadLimits::fileMb()]))
            ->visibility('public');
    }
}

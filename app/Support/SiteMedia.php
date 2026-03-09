<?php

namespace App\Support;

use App\Models\Setting;

class SiteMedia
{
    public static function logoUrl(): ?string
    {
        $path = Setting::where('key', 'site_logo')->first()?->value ?: config('clan.logo');
        if (empty($path)) {
            return null;
        }
        return str_starts_with($path, 'http') ? $path : storage_asset(ltrim($path, '/'));
    }

    public static function bannerEnabled(): bool
    {
        return (bool) filter_var(setting('site_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function bannerUrl(): ?string
    {
        if (! static::bannerEnabled()) {
            return null;
        }
        $path = Setting::where('key', 'site_banner')->first()?->value ?: config('clan.banner');
        if (empty($path)) {
            return null;
        }
        return str_starts_with($path, 'http') ? $path : storage_asset(ltrim($path, '/'));
    }

    public static function bannerLink(): ?string
    {
        $link = setting('site_banner_link', '');
        return $link !== '' ? $link : null;
    }

    public static function bannerAlt(): string
    {
        return (string) setting('site_banner_alt', '');
    }

    public static function bannerHeightClass(): string
    {
        $height = setting('site_banner_height', 'medium');
        return match ($height) {
            'small' => 'h-24 sm:h-28',
            'large' => 'h-48 sm:h-56',
            default => 'h-32 sm:h-40',
        };
    }
}

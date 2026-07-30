<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\SliderSlide;
use Illuminate\Support\Facades\Storage;

class SiteMedia
{
    public static function logoUrl(): ?string
    {
        $path = Setting::where('key', 'site_logo')->first()?->value ?: config('clan.logo');
        if (empty($path)) {
            return null;
        }

        return static::publicUrl((string) $path);
    }

    /** Inline-Styles für .site-brand-logo (Höhe aus ACP; Menü bleibt kompakt via CSS-Slot). */
    public static function logoImgStyle(): string
    {
        $h = site_logo_height_css();
        $mw = site_logo_max_width_css();
        $slot = '2.5rem';

        return "height:{$h};max-height:none;width:auto;max-width:{$mw};object-fit:contain;display:block;"
            ."margin-top:calc(({$slot} - {$h}) / 2);margin-bottom:calc(({$slot} - {$h}) / 2);";
    }

    public static function bannerEnabled(): bool
    {
        return (bool) filter_var(setting('site_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function bannerPath(): ?string
    {
        $path = Setting::where('key', 'site_banner')->first()?->value ?: config('clan.banner');
        if (empty($path)) {
            return null;
        }

        return (string) $path;
    }

    public static function bannerConfigured(): bool
    {
        return static::bannerPath() !== null;
    }

    public static function bannerUrl(): ?string
    {
        if (! static::bannerEnabled()) {
            return null;
        }
        $path = static::bannerPath();
        if ($path === null) {
            return null;
        }

        return static::publicUrl($path);
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

    public static function activeSliderCount(): int
    {
        try {
            return (int) SliderSlide::query()->where('active', true)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public static function hasActiveSlider(): bool
    {
        return static::activeSliderCount() > 0;
    }

    /**
     * Banner und Slider dürfen nicht gleichzeitig aktiv sein.
     * Bei Konflikt gewinnt der Slider – Banner wird ausgeblendet.
     */
    public static function bannerAndSliderConflict(): bool
    {
        return static::bannerEnabled()
            && static::bannerConfigured()
            && static::hasActiveSlider();
    }

    /**
     * @return array{resolved: bool, message: ?string}
     */
    public static function enforceBannerSliderExclusivity(): array
    {
        if (! static::bannerAndSliderConflict()) {
            return ['resolved' => false, 'message' => null];
        }

        set_setting('site_banner_enabled', '0');

        return [
            'resolved' => true,
            'message' => __('settings.banner_slider_conflict_auto'),
        ];
    }

    public static function disableBannerForSlider(): void
    {
        if (static::bannerEnabled()) {
            set_setting('site_banner_enabled', '0');
        }
    }

    protected static function publicUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return storage_asset(ltrim($path, '/'));
    }

    public static function deleteStoredPath(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

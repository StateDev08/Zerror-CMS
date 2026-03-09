<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;

class SiteSettingsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $title = 'Einstellungen';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.site-settings';

    public function getViewData(): array
    {
        $logo = Setting::where('key', 'site_logo')->first()?->value ?: config('clan.logo');
        $banner = Setting::where('key', 'site_banner')->first()?->value ?: config('clan.banner');
        return [
            'siteLogo' => $logo,
            'siteBanner' => $banner,
            'siteBannerEnabled' => (bool) filter_var(setting('site_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'siteBannerLink' => setting('site_banner_link', ''),
            'siteBannerAlt' => setting('site_banner_alt', ''),
            'siteBannerHeight' => setting('site_banner_height', 'medium'),
            'siteName' => setting('site_name', config('clan.name')),
            'contactAddress' => setting('contact_address', ''),
            'contactEmail' => setting('contact_email', ''),
            'contactPhone' => setting('contact_phone', ''),
            'socialDiscord' => setting('social_discord', config('clan.discord_invite_url', '')),
            'socialFacebook' => setting('social_facebook', ''),
            'socialTwitter' => setting('social_twitter', ''),
            'socialYoutube' => setting('social_youtube', ''),
            'seoDefaultTitle' => setting('seo_default_title', config('clan.name')),
            'seoDefaultDescription' => setting('seo_default_description', ''),
            'seoOgImage' => setting('seo_og_image', ''),
            'maintenanceEnabled' => (bool) filter_var(setting('maintenance_enabled', '0'), FILTER_VALIDATE_BOOLEAN),
            'authRegistrationEnabled' => (bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'applicationsEnabled' => (bool) filter_var(setting('applications_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'sliderDurationSeconds' => (int) max(2, min(30, (int) setting('slider_duration_seconds', 5))),
            'sliderHeight' => setting('slider_height', 'medium'),
            'sliderShowArrows' => (bool) filter_var(setting('slider_show_arrows', '1'), FILTER_VALIDATE_BOOLEAN),
            'sliderShowDots' => (bool) filter_var(setting('slider_show_dots', '1'), FILTER_VALIDATE_BOOLEAN),
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteMedia;
use App\Support\UploadLimits;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteMediaController extends Controller
{
    /**
     * Legacy-POST für Logo/Banner & Settings (Tests / Fallback).
     * Primär speichert die Filament-Seite SiteSettingsPage per Livewire.
     */
    public function update(Request $request): RedirectResponse
    {
        $imageKb = UploadLimits::imageKb();
        if ($request->filled('upload_max_image_mb') || $request->filled('upload_max_file_mb')) {
            $imgMb = UploadLimits::clamp((int) $request->input('upload_max_image_mb', UploadLimits::imageMb()));
            $fileMb = UploadLimits::clamp((int) $request->input('upload_max_file_mb', UploadLimits::fileMb()));
            set_setting('upload_max_image_mb', (string) $imgMb);
            set_setting('upload_max_file_mb', (string) $fileMb);
            $imageKb = $imgMb * 1024;
            UploadLimits::syncRuntimeLimits();
        }

        $validated = $request->validate([
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:'.$imageKb],
            'logo_remove' => ['nullable', 'in:0,1'],
            'banner' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:'.$imageKb],
            'banner_remove' => ['nullable', 'in:0,1'],
            'site_banner_enabled' => ['nullable', 'in:0,1'],
            'site_banner_link' => ['nullable', 'string', 'max:500'],
            'site_banner_alt' => ['nullable', 'string', 'max:255'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:1000'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'social_discord' => ['nullable', 'string', 'max:500'],
            'social_facebook' => ['nullable', 'string', 'max:500'],
            'social_twitter' => ['nullable', 'string', 'max:500'],
            'social_youtube' => ['nullable', 'string', 'max:500'],
            'social_twitch' => ['nullable', 'string', 'max:500'],
            'social_instagram' => ['nullable', 'string', 'max:500'],
            'donation_url' => ['nullable', 'string', 'max:500'],
            'discord_invite_url' => ['nullable', 'string', 'max:500'],
            'discord_webhook_url' => ['nullable', 'string', 'max:500'],
            'application_notify_email' => ['nullable', 'string', 'email', 'max:255'],
            'seo_default_title' => ['nullable', 'string', 'max:255'],
            'seo_default_description' => ['nullable', 'string', 'max:500'],
            'seo_og_image' => ['nullable', 'string', 'max:500'],
            'maintenance_enabled' => ['nullable', 'in:0,1'],
            'maintenance_message' => ['nullable', 'string', 'max:2000'],
            'auth_registration_enabled' => ['nullable', 'in:0,1'],
            'applications_enabled' => ['nullable', 'in:0,1'],
            'slider_duration_seconds' => ['nullable', 'integer', 'min:2', 'max:30'],
            'slider_show_arrows' => ['nullable', 'in:0,1'],
            'slider_show_dots' => ['nullable', 'in:0,1'],
            'footer_tagline' => ['nullable', 'string', 'max:255'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'footer_credit' => ['nullable', 'string', 'max:255'],
            'footer_credit_enabled' => ['nullable', 'in:0,1'],
            'footer_pages' => ['nullable', 'string', 'max:5000'],
            'music_dock_enabled' => ['nullable', 'in:0,1'],
            'music_dock_title' => ['nullable', 'string', 'max:100'],
            'music_dock_subtitle' => ['nullable', 'string', 'max:255'],
            'music_dock_stream_url' => ['nullable', 'string', 'max:500'],
            'nav_hardcoded_fallback' => ['nullable', 'in:0,1'],
            'cookie_banner_enabled' => ['nullable', 'in:0,1'],
            'cookie_banner_text' => ['nullable', 'string', 'max:1000'],
            'cookie_banner_button' => ['nullable', 'string', 'max:100'],
            'cookie_banner_link_label' => ['nullable', 'string', 'max:100'],
            'upload_max_image_mb' => ['nullable', 'integer', 'min:'.UploadLimits::MIN_MB, 'max:'.UploadLimits::MAX_MB],
            'upload_max_file_mb' => ['nullable', 'integer', 'min:'.UploadLimits::MIN_MB, 'max:'.UploadLimits::MAX_MB],
        ], [
            'logo.max' => UploadLimits::imageMaxMessage(__('settings.logo')),
            'banner.max' => UploadLimits::imageMaxMessage(__('settings.banner')),
        ]);

        if ($request->input('logo_remove') === '1') {
            SiteMedia::deleteStoredPath(setting('site_logo'));
            set_setting('site_logo', '');
        }

        if ($request->hasFile('logo')) {
            $old = setting('site_logo');
            $path = $request->file('logo')->store('site', 'public');
            set_setting('site_logo', $path);
            if ($old && $old !== $path) {
                SiteMedia::deleteStoredPath($old);
            }
        }

        if ($request->input('banner_remove') === '1') {
            SiteMedia::deleteStoredPath(setting('site_banner'));
            set_setting('site_banner', '');
        }

        if ($request->hasFile('banner')) {
            $old = setting('site_banner');
            $path = $request->file('banner')->store('site', 'public');
            set_setting('site_banner', $path);
            if ($old && $old !== $path) {
                SiteMedia::deleteStoredPath($old);
            }
        }

        $keys = [
            'site_name', 'contact_address', 'contact_email', 'contact_phone',
            'social_discord', 'social_facebook', 'social_twitter', 'social_youtube',
            'social_twitch', 'social_instagram', 'donation_url',
            'discord_invite_url', 'discord_webhook_url', 'application_notify_email',
            'seo_default_title', 'seo_default_description', 'seo_og_image',
            'maintenance_enabled', 'maintenance_message',
            'auth_registration_enabled', 'applications_enabled',
            'site_banner_enabled', 'site_banner_link', 'site_banner_alt',
            'slider_duration_seconds', 'slider_show_arrows', 'slider_show_dots',
            'footer_tagline', 'footer_copyright', 'footer_credit', 'footer_credit_enabled', 'footer_pages',
            'music_dock_enabled', 'music_dock_title', 'music_dock_subtitle', 'music_dock_stream_url',
            'nav_hardcoded_fallback',
            'cookie_banner_enabled', 'cookie_banner_text', 'cookie_banner_button', 'cookie_banner_link_label',
        ];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                set_setting($key, (string) $request->input($key, ''));
            }
        }

        // Ein Discord-Link: social_discord ist Eingabe; Invite spiegelt denselben Wert.
        $invite = trim((string) setting('discord_invite_url', ''));
        $socialDiscord = trim((string) setting('social_discord', ''));
        $discordUrl = $socialDiscord !== '' ? $socialDiscord : $invite;
        set_setting('social_discord', $discordUrl);
        set_setting('discord_invite_url', $discordUrl);

        if ($request->filled('upload_max_image_mb')) {
            set_setting('upload_max_image_mb', (string) UploadLimits::clamp((int) $request->input('upload_max_image_mb')));
        }
        if ($request->filled('upload_max_file_mb')) {
            set_setting('upload_max_file_mb', (string) UploadLimits::clamp((int) $request->input('upload_max_file_mb')));
        }
        UploadLimits::syncRuntimeLimits();

        $conflict = SiteMedia::enforceBannerSliderExclusivity();
        $redirect = redirect()->back()->with('success', __('settings.saved'));
        if ($conflict['resolved']) {
            $redirect->with('warning', $conflict['message']);
        }

        return $redirect;
    }
}

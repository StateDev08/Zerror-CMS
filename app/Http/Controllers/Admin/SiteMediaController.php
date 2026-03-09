<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteMediaController extends Controller
{
    /**
     * Alle Einstellungen inkl. Logo & Banner per Form-POST speichern.
     */
    public function update(Request $request): RedirectResponse
    {
        $maxKb = 2 * 1024 * 1024; // 2 GB
        $validated = $request->validate([
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:' . $maxKb],
            'banner' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:' . $maxKb],
            'banner_remove' => ['nullable', 'in:0,1'],
            'site_banner_enabled' => ['nullable', 'in:0,1'],
            'site_banner_link' => ['nullable', 'string', 'max:500'],
            'site_banner_alt' => ['nullable', 'string', 'max:255'],
            'site_banner_height' => ['nullable', 'string', 'in:small,medium,large'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:1000'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'social_discord' => ['nullable', 'string', 'max:500'],
            'social_facebook' => ['nullable', 'string', 'max:500'],
            'social_twitter' => ['nullable', 'string', 'max:500'],
            'social_youtube' => ['nullable', 'string', 'max:500'],
            'seo_default_title' => ['nullable', 'string', 'max:255'],
            'seo_default_description' => ['nullable', 'string', 'max:500'],
            'seo_og_image' => ['nullable', 'string', 'max:500'],
            'maintenance_enabled' => ['nullable', 'in:0,1'],
            'auth_registration_enabled' => ['nullable', 'in:0,1'],
            'applications_enabled' => ['nullable', 'in:0,1'],
            'slider_duration_seconds' => ['nullable', 'integer', 'min:2', 'max:30'],
            'slider_height' => ['nullable', 'string', 'in:small,medium,large'],
            'slider_show_arrows' => ['nullable', 'in:0,1'],
            'slider_show_dots' => ['nullable', 'in:0,1'],
        ], [
            'logo.max' => 'Das Logo darf maximal 2 GB groß sein. PHP upload_max_filesize und post_max_size prüfen.',
            'banner.max' => 'Der Banner darf maximal 2 GB groß sein. PHP upload_max_filesize und post_max_size prüfen.',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site', 'public');
            set_setting('site_logo', $path);
        }
        if ($request->input('banner_remove') === '1') {
            $oldPath = setting('site_banner');
            if ($oldPath && ! str_starts_with($oldPath, 'http') && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            set_setting('site_banner', '');
        }
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('site', 'public');
            set_setting('site_banner', $path);
        }

        $keys = [
            'site_name', 'contact_address', 'contact_email', 'contact_phone',
            'social_discord', 'social_facebook', 'social_twitter', 'social_youtube',
            'seo_default_title', 'seo_default_description', 'seo_og_image',
            'maintenance_enabled', 'auth_registration_enabled', 'applications_enabled',
            'site_banner_enabled', 'site_banner_link', 'site_banner_alt', 'site_banner_height',
            'slider_duration_seconds', 'slider_height', 'slider_show_arrows', 'slider_show_dots',
        ];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                set_setting($key, $request->input($key, ''));
            }
        }

        return redirect()->back()->with('success', __('settings.saved'));
    }
}

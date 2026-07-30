<?php

namespace App\Support;

/**
 * Zentrale Leseschicht für öffentlich sichtbare Site-Inhalte aus Settings (ACP),
 * mit sinnvollen Fallbacks auf Config/Lang — keine Theme-Hardcodes.
 */
class SiteContent
{
    /**
     * Footer-Rechtsseiten: [slug => Anzeige-Label].
     *
     * @return array<string, string>
     */
    public static function footerPages(): array
    {
        $raw = trim((string) setting('footer_pages', ''));
        if ($raw !== '') {
            $parsed = self::parseLabelSlugLines($raw);
            if ($parsed !== []) {
                return $parsed;
            }
        }

        $out = [];
        foreach (config('clan.footer_pages', []) as $slug => $labelKey) {
            $slug = (string) $slug;
            if ($slug === '') {
                continue;
            }
            $out[$slug] = is_string($labelKey) && str_contains($labelKey, '.')
                ? (string) __($labelKey)
                : (string) $labelKey;
        }

        return $out;
    }

    /**
     * Standard-Zeilen für das ACP-Textfeld (Label|slug).
     */
    public static function footerPagesRawDefault(): string
    {
        $raw = trim((string) setting('footer_pages', ''));
        if ($raw !== '') {
            return $raw;
        }

        $lines = [];
        foreach (self::footerPages() as $slug => $label) {
            $lines[] = $label.'|'.$slug;
        }

        return implode("\n", $lines);
    }

    public static function footerTagline(): string
    {
        return trim((string) setting('footer_tagline', ''));
    }

    public static function footerCopyright(): string
    {
        $value = trim((string) setting('footer_copyright', ''));

        return $value !== '' ? $value : (string) __('footer.copyright');
    }

    public static function footerCredit(): string
    {
        if (! filter_var(setting('footer_credit_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return '';
        }

        $value = trim((string) setting('footer_credit', ''));

        return $value !== '' ? $value : (string) __('footer.developed_by');
    }

    public static function musicDockEnabled(): bool
    {
        return (bool) filter_var(setting('music_dock_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function musicDockTitle(): string
    {
        $value = trim((string) setting('music_dock_title', ''));

        return $value !== '' ? $value : (string) __('settings.music_dock_title_default');
    }

    public static function musicDockSubtitle(): string
    {
        $value = trim((string) setting('music_dock_subtitle', ''));

        return $value !== '' ? $value : (string) __('settings.music_dock_subtitle_default');
    }

    public static function musicDockStreamUrl(): string
    {
        return trim((string) setting('music_dock_stream_url', ''));
    }

    public static function navHardcodedFallback(): bool
    {
        return (bool) filter_var(setting('nav_hardcoded_fallback', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function cookieBannerEnabled(): bool
    {
        return (bool) filter_var(setting('cookie_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function cookieBannerText(): string
    {
        $value = trim((string) setting('cookie_banner_text', ''));

        return $value !== '' ? $value : (string) __('cookies.banner_text');
    }

    public static function cookieBannerButton(): string
    {
        $value = trim((string) setting('cookie_banner_button', ''));

        return $value !== '' ? $value : (string) __('cookies.accept');
    }

    public static function cookieBannerLinkLabel(): string
    {
        $value = trim((string) setting('cookie_banner_link_label', ''));

        return $value !== '' ? $value : (string) __('nav.cookies');
    }

    public static function maintenanceMessage(): string
    {
        $value = trim((string) setting('maintenance_message', ''));

        return $value !== '' ? $value : (string) __('maintenance.message');
    }

    public static function donationUrl(): string
    {
        return trim((string) (
            setting('donation_url', '')
            ?: config('clan.donation_url', '')
        ));
    }

    public static function discordWebhookUrl(): string
    {
        return trim((string) (
            setting('discord_webhook_url', '')
            ?: config('clan.discord_webhook_url', '')
        ));
    }

    public static function applicationNotifyEmail(): string
    {
        return trim((string) (
            setting('application_notify_email', '')
            ?: config('clan.application_notify_email', '')
        ));
    }

    public static function homeCtaPrimaryLabel(): string
    {
        $value = trim((string) setting('home_cta_primary_label', ''));

        return $value !== '' ? $value : (string) __('nav.apply');
    }

    public static function homeCtaPrimaryUrl(): string
    {
        $value = trim((string) setting('home_cta_primary_url', ''));

        return $value !== '' ? $value : route('apply.index');
    }

    public static function homeCtaSecondaryLabel(): string
    {
        $value = trim((string) setting('home_cta_secondary_label', ''));

        return $value !== '' ? $value : (string) __('nav.news');
    }

    public static function homeCtaSecondaryUrl(): string
    {
        $value = trim((string) setting('home_cta_secondary_url', ''));

        return $value !== '' ? $value : route('news.index');
    }

    /**
     * @return array<string, string> label => slug mapped as slug => label
     */
    public static function parseLabelSlugLines(string $raw): array
    {
        $out = [];
        foreach (preg_split('/\R/u', $raw) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = array_map('trim', explode('|', $line, 2));
            if (count($parts) === 1) {
                $slug = self::slugify($parts[0]);
                $label = $parts[0];
            } else {
                // Bevorzugt Label|slug (z. B. Impressum|impressum)
                if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $parts[1])) {
                    $label = $parts[0];
                    $slug = $parts[1];
                } elseif (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $parts[0])) {
                    $slug = $parts[0];
                    $label = $parts[1];
                } else {
                    $label = $parts[0];
                    $slug = self::slugify($parts[1] !== '' ? $parts[1] : $parts[0]);
                }
            }
            if ($slug === '' || $label === '') {
                continue;
            }
            $out[$slug] = $label;
        }

        return $out;
    }

    protected static function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';

        return trim($value, '-');
    }
}

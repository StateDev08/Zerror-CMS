<?php

if (! function_exists('setting')) {
    /**
     * Wert aus der settings-Tabelle lesen (Backend-editierbar).
     * Optional mit Cache für Performance.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'setting.' . $key,
            300,
            function () use ($key, $default) {
                $value = \App\Models\Setting::where('key', $key)->value('value');
                return $value !== null ? $value : $default;
            }
        );
    }
}

if (! function_exists('set_setting')) {
    /**
     * Wert in der settings-Tabelle speichern. Cache für diesen Key invalidieren.
     */
    function set_setting(string $key, mixed $value): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_string($value) ? $value : (string) $value]
        );
        \Illuminate\Support\Facades\Cache::forget('setting.' . $key);
    }
}

if (! function_exists('storage_asset')) {
    /**
     * URL für eine Datei aus storage/app/public (über Laravel-Route, funktioniert ohne Symlink).
     */
    function storage_asset(string $path): string
    {
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return url('app-storage/' . $path);
    }
}

if (! function_exists('module_config')) {
    /** @return array<string, mixed> */
    function module_config(string $name): array
    {
        $setting = \App\Models\Setting::where('key', 'module_' . $name . '_config')->first();
        if (! $setting || empty($setting->value)) {
            return [];
        }
        $decoded = json_decode($setting->value, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (! function_exists('set_module_config')) {
    /** @param array<string, mixed> $data */
    function set_module_config(string $name, array $data): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'module_' . $name . '_config'],
            ['value' => json_encode($data)]
        );
    }
}

if (! function_exists('plugin_config')) {
    /** @return array<string, mixed> */
    function plugin_config(string $name): array
    {
        $setting = \App\Models\Setting::where('key', 'plugin_' . $name . '_config')->first();
        if (! $setting || empty($setting->value)) {
            return [];
        }
        $decoded = json_decode($setting->value, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (! function_exists('set_plugin_config')) {
    /** @param array<string, mixed> $data */
    function set_plugin_config(string $name, array $data): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'plugin_' . $name . '_config'],
            ['value' => json_encode($data)]
        );
    }
}

if (! function_exists('opens_in_new_tab')) {
    /**
     * Externe URLs, Spezial-Protokolle und Admin-Links öffnen in neuem Tab.
     */
    function opens_in_new_tab(?string $url): bool
    {
        $url = trim((string) $url);
        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, 'javascript:')) {
            return false;
        }

        $lower = strtolower($url);
        if (str_starts_with($lower, 'mailto:') || str_starts_with($lower, 'tel:')) {
            return false;
        }

        // ts3server://, discord://, steam:// …
        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url) && ! preg_match('#^https?:#i', $url)) {
            return true;
        }

        try {
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '';
            if (isset($parsed['host'])) {
                $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
                $host = strtolower((string) $parsed['host']);
                if ($appHost && strtolower((string) $appHost) !== $host) {
                    return true;
                }
            }
            if (is_string($path) && preg_match('#(^|/)admin(/|$)#', $path)) {
                return true;
            }
        } catch (\Throwable $e) {
            return str_starts_with($lower, 'http');
        }

        return false;
    }
}

if (! function_exists('link_new_tab_attrs')) {
    /** HTML-Attribute target/rel für Links, die in neuem Tab öffnen sollen. */
    function link_new_tab_attrs(?string $url): string
    {
        if (! opens_in_new_tab($url)) {
            return '';
        }

        return ' target="_blank" rel="noopener noreferrer"';
    }
}

if (! function_exists('module_enabled')) {
    /** Ob ein Modul in der DB aktiviert ist. */
    function module_enabled(string $name): bool
    {
        try {
            return app(\App\Support\ModuleManager::class)->isEnabled($name);
        } catch (\Throwable) {
            return false;
        }
    }
}

if (! function_exists('system_module_enabled')) {
    /** Ob ein System-Modul in der DB aktiviert ist. */
    function system_module_enabled(string $name): bool
    {
        try {
            return app(\App\Support\SystemModuleManager::class)->isEnabled($name);
        } catch (\Throwable) {
            return false;
        }
    }
}

if (! function_exists('html_content')) {
    /** Editor-/CMS-Inhalt sicher als HTML ausgeben (Klartext oder Rich-HTML). */
    function html_content(?string $content): \Illuminate\Support\HtmlString
    {
        return \App\Support\HtmlContent::toHtml($content);
    }
}

if (! function_exists('site_name')) {
    /** Clan-/Seitenname aus Settings (Fallback: config clan.name). */
    function site_name(): string
    {
        $name = trim((string) setting('site_name', config('clan.name', 'ZerroCMS')));

        return $name !== '' ? $name : (string) config('clan.name', 'ZerroCMS');
    }
}

if (! function_exists('site_timezone')) {
    /** Site-Zeitzone aus Settings (Default Europe/Berlin). */
    function site_timezone(): string
    {
        $tz = trim((string) setting('app_timezone', ''));
        if ($tz !== '') {
            try {
                new \DateTimeZone($tz);

                return $tz;
            } catch (\Throwable) {
                // ungültig → Fallback
            }
        }

        return 'Europe/Berlin';
    }
}

if (! function_exists('site_date_format')) {
    function site_date_format(): string
    {
        $fmt = trim((string) setting('date_format', 'd.m.Y'));

        return $fmt !== '' ? $fmt : 'd.m.Y';
    }
}

if (! function_exists('site_time_format')) {
    function site_time_format(): string
    {
        $fmt = trim((string) setting('time_format', 'H:i'));

        return $fmt !== '' ? $fmt : 'H:i';
    }
}

if (! function_exists('site_datetime_format')) {
    function site_datetime_format(): string
    {
        return site_date_format().' '.site_time_format();
    }
}

if (! function_exists('site_logo_height_css')) {
    /**
     * Logo-Höhe aus Settings: "80", "80px", "3rem" oder "200%" (bezogen auf 40px Basis).
     */
    function site_logo_height_css(): string
    {
        $raw = trim((string) setting('site_logo_height', ''));
        $basePx = 40.0;

        if ($raw === '') {
            return '2.5rem';
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*%$/u', $raw, $m)) {
            $pct = max(25.0, min(400.0, (float) $m[1]));

            return round($basePx * ($pct / 100), 2).'px';
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*(px|rem|em)$/iu', $raw, $m)) {
            $num = (float) $m[1];
            $unit = strtolower($m[2]);
            if ($unit === 'px') {
                $num = max(16.0, min(240.0, $num));
            } else {
                $num = max(1.0, min(15.0, $num));
            }

            return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.').$unit;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)$/u', $raw, $m)) {
            $num = max(16.0, min(240.0, (float) $m[1]));

            return (string) (int) round($num).'px';
        }

        return '2.5rem';
    }
}

if (! function_exists('site_logo_max_width_css')) {
    function site_logo_max_width_css(): string
    {
        return 'calc('.site_logo_height_css().' * 4.4)';
    }
}

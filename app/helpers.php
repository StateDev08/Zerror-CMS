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

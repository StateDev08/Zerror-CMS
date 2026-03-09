<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected string $themesPath;

    protected ?string $activeTheme = null;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        $this->activeTheme = config('clan.theme', 'default');
    }

    public function active(): string
    {
        if (! Installer::isInstalled()) {
            return $this->activeTheme;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return $this->activeTheme;
            }
            $fromDb = \App\Models\Setting::where('key', 'theme')->first();
            if ($fromDb) {
                return $fromDb->value ?? $this->activeTheme;
            }
        } catch (\Throwable $e) {
            // DB nicht erreichbar oder Fehler → Fallback auf Config-Theme
        }
        return $this->activeTheme;
    }

    public function setActive(string $name): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => $name]
        );
        $this->activeTheme = $name;
    }

    /**
     * @return array<string, array{name: string, path: string, version?: string}>
     */
    public function discover(): array
    {
        $out = [];
        if (! File::isDirectory($this->themesPath)) {
            return $out;
        }
        foreach (File::directories($this->themesPath) as $dir) {
            $manifestPath = $dir . DIRECTORY_SEPARATOR . 'theme.json';
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                // JSON invalid → skip theme to avoid "Undefined array key"
                if (! is_array($manifest)) {
                    continue;
                }
                $name = $manifest['name'] ?? basename($dir);
                $out[$name] = [
                    'name' => $name,
                    'path' => $dir,
                    'version' => $manifest['version'] ?? null,
                    'parent' => $manifest['parent'] ?? null,
                ];
            }
        }
        return $out;
    }

    public function registerViewNamespace(): void
    {
        $active = $this->active();
        $defaultViewsPath = $this->themesPath . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views';
        $activeViewsPath = $this->themesPath . DIRECTORY_SEPARATOR . $active . DIRECTORY_SEPARATOR . 'views';

        $paths = [];
        if (File::isDirectory($activeViewsPath)) {
            $paths[] = $activeViewsPath;
        }
        if (File::isDirectory($defaultViewsPath) && ! in_array($defaultViewsPath, $paths)) {
            $paths[] = $defaultViewsPath;
        }
        if ($paths !== []) {
            View::addNamespace('theme', $paths);
        }
    }

    public function themePath(string $theme = null): string
    {
        $theme = $theme ?? $this->active();
        $path = $this->themesPath . DIRECTORY_SEPARATOR . $theme;
        // Theme directory missing → fallback to default
        if (! File::isDirectory($path) && $theme !== 'default') {
            $path = $this->themesPath . DIRECTORY_SEPARATOR . 'default';
        }
        return $path;
    }

    public function assetUrl(string $path, string $theme = null): string
    {
        $theme = $theme ?? $this->active();
        $fullPath = $this->themesPath . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ltrim($path, '/');
        if (File::exists($fullPath)) {
            return asset('themes/' . $theme . '/' . ltrim($path, '/'));
        }
        return asset($path);
    }

    /**
     * Theme-Farben aus settings lesen (für CSS-Variablen).
     *
     * @return array<string, string>
     */
    public function getThemeColors(): array
    {
        $defaults = [
            'primary' => '#3b82f6',
            'accent' => '#10b981',
            'background' => '#f9fafb',
            'surface' => '#ffffff',
            'text' => '#111827',
            'text_muted' => '#6b7280',
        ];
        if (! Installer::isInstalled()) {
            return $defaults;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return $defaults;
            }
            foreach (array_keys($defaults) as $key) {
                $setting = \App\Models\Setting::where('key', 'theme_' . $key)->first();
                if ($setting && $setting->value) {
                    $defaults[$key] = $setting->value;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return $defaults;
    }

    public function setThemeColor(string $key, string $value): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_' . $key],
            ['value' => $value]
        );
    }

    /**
     * Layout-Optionen aus settings lesen (Sidebar-Positionen, Main-Reihenfolge).
     *
     * @return array{nav_sidebar_position: string, widget_sidebar_position: string, main_order: string}
     */
    public function getLayoutOptions(): array
    {
        $defaults = [
            'nav_sidebar_position' => 'left',
            'widget_sidebar_position' => 'right',
            'main_order' => 'content_first',
        ];
        if (! Installer::isInstalled()) {
            return $defaults;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return $defaults;
            }
            $keys = [
                'nav_sidebar_position' => 'theme_nav_sidebar_position',
                'widget_sidebar_position' => 'theme_widget_sidebar_position',
                'main_order' => 'theme_main_order',
            ];
            foreach ($keys as $optionKey => $settingKey) {
                $setting = \App\Models\Setting::where('key', $settingKey)->first();
                if ($setting && $setting->value) {
                    $defaults[$optionKey] = $setting->value;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return $defaults;
    }

    public function setLayoutOption(string $key, string $value): void
    {
        $settingKey = 'theme_' . $key;
        \App\Models\Setting::updateOrCreate(
            ['key' => $settingKey],
            ['value' => $value]
        );
    }

    /**
     * Standard Dark-Mode für neue Besucher (ohne Cookie): light, dark, system.
     */
    public function getDefaultThemeMode(): string
    {
        if (! Installer::isInstalled()) {
            return 'system';
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return 'system';
            }
            $setting = \App\Models\Setting::where('key', 'theme_default_mode')->first();
            if ($setting && \in_array($setting->value, ['light', 'dark', 'system'], true)) {
                return $setting->value;
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return 'system';
    }

    public function setDefaultThemeMode(string $value): void
    {
        if (! \in_array($value, ['light', 'dark', 'system'], true)) {
            return;
        }
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_default_mode'],
            ['value' => $value]
        );
    }
}

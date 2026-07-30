<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected string $themesPath;

    protected ?string $activeTheme = null;

    public const FALLBACK = 'common';

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        $this->activeTheme = config('clan.theme', 'pax-dei');
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
            if ($fromDb && $fromDb->value) {
                return $fromDb->value;
            }
        } catch (\Throwable $e) {
            // ignore
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
     * @return array<string, array{name: string, path: string, version?: string, label?: string, selectable?: bool, colors?: array}>
     */
    public function discover(bool $selectableOnly = false): array
    {
        $out = [];
        if (! File::isDirectory($this->themesPath)) {
            return $out;
        }
        foreach (File::directories($this->themesPath) as $dir) {
            $manifestPath = $dir.DIRECTORY_SEPARATOR.'theme.json';
            if (! File::exists($manifestPath)) {
                continue;
            }
            $manifest = json_decode(File::get($manifestPath), true);
            if (! is_array($manifest)) {
                continue;
            }
            $name = $manifest['name'] ?? basename($dir);
            $selectable = array_key_exists('selectable', $manifest)
                ? (bool) $manifest['selectable']
                : ($name !== self::FALLBACK);
            if ($selectableOnly && ! $selectable) {
                continue;
            }
            $out[$name] = [
                'name' => $name,
                'path' => $dir,
                'version' => $manifest['version'] ?? null,
                'parent' => $manifest['parent'] ?? null,
                'label' => $manifest['label'] ?? $name,
                'selectable' => $selectable,
                'description' => $manifest['description'] ?? null,
                'colors' => $manifest['colors'] ?? [],
                'fonts' => is_array($manifest['fonts'] ?? null) ? $manifest['fonts'] : [],
            ];
        }

        return $out;
    }

    public function registerViewNamespace(): void
    {
        $active = $this->active();
        $fallbackViewsPath = $this->themesPath.DIRECTORY_SEPARATOR.self::FALLBACK.DIRECTORY_SEPARATOR.'views';
        $activeViewsPath = $this->themesPath.DIRECTORY_SEPARATOR.$active.DIRECTORY_SEPARATOR.'views';

        $paths = [];
        if (File::isDirectory($activeViewsPath)) {
            $paths[] = $activeViewsPath;
        }
        if (File::isDirectory($fallbackViewsPath) && ! in_array($fallbackViewsPath, $paths, true)) {
            $paths[] = $fallbackViewsPath;
        }
        if ($paths !== []) {
            View::addNamespace('theme', $paths);
        }
    }

    public function themePath(?string $theme = null): string
    {
        $theme = $theme ?? $this->active();
        $path = $this->themesPath.DIRECTORY_SEPARATOR.$theme;
        if (! File::isDirectory($path) && $theme !== self::FALLBACK) {
            $path = $this->themesPath.DIRECTORY_SEPARATOR.self::FALLBACK;
        }

        return $path;
    }

    public function assetUrl(string $path, ?string $theme = null): string
    {
        $theme = $theme ?? $this->active();
        $fullPath = $this->themesPath.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.ltrim($path, '/');
        if (File::exists($fullPath)) {
            return asset('themes/'.$theme.'/'.ltrim($path, '/'));
        }

        return asset($path);
    }

    /**
     * @return array<string, string>
     */
    public function getThemeColors(): array
    {
        $defaults = [
            'primary' => '#c9a227',
            'accent' => '#3b82f6',
            'background' => '#0a0a0c',
            'surface' => '#141418',
            'text' => '#f5f5f4',
            'text_muted' => '#a8a29e',
        ];

        $active = $this->active();
        $themes = $this->discover();
        if (isset($themes[$active]['colors']) && is_array($themes[$active]['colors'])) {
            $defaults = array_merge($defaults, array_filter($themes[$active]['colors']));
        }

        if (! Installer::isInstalled()) {
            return $defaults;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return $defaults;
            }
            foreach (array_keys($defaults) as $key) {
                $setting = \App\Models\Setting::where('key', 'theme_'.$key)->first();
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
            ['key' => 'theme_'.$key],
            ['value' => $value]
        );
    }

    /**
     * Font tokens for the active theme (ACP-overridable).
     *
     * @return array{display: string, body: string, url: string}
     */
    public function getThemeFonts(): array
    {
        $defaults = [
            'display' => 'Fraunces',
            'body' => 'Source Sans 3',
            'url' => 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Source+Sans+3:wght@400;500;600;700&display=swap',
        ];

        $active = $this->active();
        $themes = $this->discover();
        if (isset($themes[$active]['fonts']) && is_array($themes[$active]['fonts'])) {
            $defaults = array_merge($defaults, array_filter([
                'display' => $themes[$active]['fonts']['display'] ?? null,
                'body' => $themes[$active]['fonts']['body'] ?? null,
                'url' => $themes[$active]['fonts']['url'] ?? null,
            ]));
        }

        if (! Installer::isInstalled()) {
            return $defaults;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return $defaults;
            }
            foreach (['display', 'body', 'url'] as $key) {
                $setting = \App\Models\Setting::where('key', 'theme_font_'.$key)->first();
                if ($setting && is_string($setting->value) && $setting->value !== '') {
                    $defaults[$key] = $setting->value;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $defaults;
    }

    public function setThemeFont(string $key, string $value): void
    {
        if (! in_array($key, ['display', 'body', 'url'], true)) {
            return;
        }
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_font_'.$key],
            ['value' => $value]
        );
        if (function_exists('set_setting')) {
            \Illuminate\Support\Facades\Cache::forget('setting.theme_font_'.$key);
        }
    }

    /**
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
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_'.$key],
            ['value' => $value]
        );
    }

    public function getDefaultThemeMode(): string
    {
        if (! Installer::isInstalled()) {
            return 'dark';
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return 'dark';
            }
            $setting = \App\Models\Setting::where('key', 'theme_default_mode')->first();
            if ($setting && in_array($setting->value, ['light', 'dark', 'system'], true)) {
                return $setting->value;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'dark';
    }

    /**
     * Erhöht sich, wenn der Site-Default-Modus geändert wird.
     * Alte User-Cookies ohne passende Revision greifen dann nicht mehr.
     */
    public function getThemeModeRevision(): int
    {
        if (! Installer::isInstalled()) {
            return 1;
        }
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return 1;
            }
            $raw = \App\Models\Setting::where('key', 'theme_mode_revision')->value('value');

            return max(1, (int) $raw);
        } catch (\Throwable $e) {
            return 1;
        }
    }

    public function setDefaultThemeMode(string $value): void
    {
        if (! in_array($value, ['light', 'dark', 'system'], true)) {
            return;
        }
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_default_mode'],
            ['value' => $value]
        );
        $revision = $this->getThemeModeRevision() + 1;
        \App\Models\Setting::updateOrCreate(
            ['key' => 'theme_mode_revision'],
            ['value' => (string) $revision]
        );
        if (function_exists('set_setting')) {
            // Cache der Hilfsfunktion invalidieren, falls genutzt
            \Illuminate\Support\Facades\Cache::forget('setting.theme_default_mode');
            \Illuminate\Support\Facades\Cache::forget('setting.theme_mode_revision');
        }
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

class PluginManager
{
    protected string $pluginsPath;

    /** @var array<string, bool> */
    protected array $booted = [];

    /** @var array<int, callable(): string> */
    protected array $headHtmlCallables = [];

    /** @var array<int, callable(): string> */
    protected array $bodyStartHtmlCallables = [];

    public function __construct()
    {
        $this->pluginsPath = base_path('plugins');
    }

    /**
     * @return array<string, array{name: string, enabled: bool, path: string}>
     */
    public function discover(): array
    {
        $out = [];
        if (! File::isDirectory($this->pluginsPath)) {
            return $out;
        }
        foreach (File::directories($this->pluginsPath) as $dir) {
            $manifestPath = $dir . DIRECTORY_SEPARATOR . 'plugin.json';
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                // JSON invalid → skip plugin
                if (! is_array($manifest)) {
                    continue;
                }
                $name = $manifest['name'] ?? basename($dir);
                $out[$name] = [
                    'name' => $name,
                    'enabled' => $this->isEnabled($name),
                    'path' => $dir,
                    'manifest' => $manifest,
                ];
            }
        }
        return $out;
    }

    public function isEnabled(string $name): bool
    {
        $plugin = \App\Models\Plugin::where('name', $name)->first();
        return $plugin ? $plugin->enabled : false;
    }

    public function bootEnabled(): void
    {
        $plugins = \App\Models\Plugin::where('enabled', true)->orderBy('order')->get();
        foreach ($plugins as $plugin) {
            $this->boot($plugin->name);
        }
    }

    public function boot(string $name): void
    {
        if (isset($this->booted[$name])) {
            return;
        }
        $discovered = $this->discover();
        if (! isset($discovered[$name])) {
            return;
        }
        $manifest = $discovered[$name]['manifest'] ?? [];
        $providerClass = $manifest['provider'] ?? null;
        if ($providerClass && class_exists($providerClass)) {
            app()->register($providerClass);
        }
        $this->booted[$name] = true;
    }

    public function fireMenuBuilding(array &$items): void
    {
        Event::dispatch('zerrocms.menu.building', [&$items]);
    }

    public function fireFooterSlots(array &$slots): void
    {
        Event::dispatch('zerrocms.footer.slots', [&$slots]);
    }

    /**
     * Config-Schema für ein Plugin (aus manifest configSchema).
     *
     * @return array<int, array{key: string, type: string, label: string, default: mixed}>
     */
    public function getConfigSchema(string $name): array
    {
        $discovered = $this->discover();
        if (! isset($discovered[$name]['manifest']['configSchema'])) {
            return [];
        }
        $schema = $discovered[$name]['manifest']['configSchema'];
        return is_array($schema) ? $schema : [];
    }

    public function registerHeadHtml(callable $fn): void
    {
        $this->headHtmlCallables[] = $fn;
    }

    public function registerBodyStartHtml(callable $fn): void
    {
        $this->bodyStartHtmlCallables[] = $fn;
    }

    public function getHeadHtml(): string
    {
        $out = '';
        foreach ($this->headHtmlCallables as $fn) {
            try {
                $html = $fn();
                if (is_string($html) && $html !== '') {
                    $out .= $html;
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return $out;
    }

    public function getBodyStartHtml(): string
    {
        $out = '';
        foreach ($this->bodyStartHtmlCallables as $fn) {
            try {
                $html = $fn();
                if (is_string($html) && $html !== '') {
                    $out .= $html;
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return $out;
    }
}

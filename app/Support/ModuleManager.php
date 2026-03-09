<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class ModuleManager
{
    protected string $modulesPath;

    /** @var array<string, bool> */
    protected array $loaded = [];

    public function __construct()
    {
        $this->modulesPath = base_path('modules');
    }

    public function isEnabled(string $name): bool
    {
        $module = \App\Models\Module::where('name', $name)->first();
        return $module ? $module->enabled : false;
    }

    /**
     * @return array<string, array{name: string, enabled: bool, path: string, version?: string, description?: string}>
     */
    public function discover(): array
    {
        $out = [];
        if (! File::isDirectory($this->modulesPath)) {
            return $out;
        }
        foreach (File::directories($this->modulesPath) as $dir) {
            $name = basename($dir);
            $manifest = $this->getManifest($name);
            $out[$name] = [
                'name' => $manifest['name'] ?? $name,
                'enabled' => $this->isEnabled($name),
                'path' => $dir,
                'version' => $manifest['version'] ?? null,
                'description' => $manifest['description'] ?? null,
            ];
        }
        return $out;
    }

    public function loadEnabled(): void
    {
        $modules = \App\Models\Module::where('enabled', true)->get();
        foreach ($modules as $module) {
            $this->load($module->name);
        }
    }

    public function load(string $name): void
    {
        if (isset($this->loaded[$name])) {
            return;
        }
        $path = $this->modulesPath . DIRECTORY_SEPARATOR . $name;
        if (! File::isDirectory($path)) {
            return;
        }
        $providerPath = $path . DIRECTORY_SEPARATOR . 'ModuleServiceProvider.php';
        if (File::exists($providerPath)) {
            try {
                // require can throw (parse/syntax) → log and skip module
                $providerClass = require $providerPath;
                if (is_string($providerClass) && class_exists($providerClass)) {
                    app()->register($providerClass);
                }
            } catch (\Throwable $e) {
                report($e);
                // Skip this module, do not mark as loaded so it can be retried later
                return;
            }
        }
        $this->loaded[$name] = true;
    }

    /**
     * Optionale Metadaten aus module.json (name, version, description).
     *
     * @return array{name?: string, version?: string, description?: string}
     */
    public function getManifest(string $name): array
    {
        $path = $this->modulesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'module.json';
        if (! File::exists($path)) {
            return [];
        }
        $json = json_decode(File::get($path), true);
        if (! is_array($json)) {
            return [];
        }
        $out = [];
        if (isset($json['name']) && is_string($json['name'])) {
            $out['name'] = $json['name'];
        }
        if (isset($json['version']) && is_string($json['version'])) {
            $out['version'] = $json['version'];
        }
        if (isset($json['description']) && is_string($json['description'])) {
            $out['description'] = $json['description'];
        }
        return $out;
    }

    /**
     * Config-Schema für ein Modul (aus config.json im Modulordner).
     *
     * @return array<int, array{key: string, type: string, label: string, default: mixed}>
     */
    public function getConfigSchema(string $name): array
    {
        $path = $this->modulesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.json';
        if (! File::exists($path)) {
            return [];
        }
        $json = json_decode(File::get($path), true);
        // Invalid JSON → return empty schema so form still renders
        return is_array($json) ? $json : [];
    }
}

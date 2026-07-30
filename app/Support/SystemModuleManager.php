<?php

namespace App\Support;

use App\Models\SystemModule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Lädt und verwaltet System-Module unter system-modules/{id}/.
 * Eigenständig – kein Ableger von ModuleManager.
 */
class SystemModuleManager
{
    protected string $basePath;

    /** @var array<string, bool> */
    protected array $loaded = [];

    /**
     * ACP-Aktionen, die System-Module in ihrem Provider registrieren.
     *
     * @var array<string, array<string, array{id: string, label: string, color?: string, confirm?: string, outlined?: bool, handler: callable}>>
     */
    protected array $adminActions = [];

    public function __construct()
    {
        $this->basePath = base_path('system-modules');
    }

    /**
     * Registriert eine ACP-Aktion für die System-Module-Karte (kein Kingshot-Hardcoding im Core).
     *
     * @param  array{id: string, label: string, color?: string, confirm?: string, outlined?: bool, handler: callable}  $action
     */
    public function registerAdminAction(string $moduleId, array $action): void
    {
        $id = (string) ($action['id'] ?? '');
        if ($id === '' || ! is_callable($action['handler'] ?? null)) {
            return;
        }

        $this->adminActions[$moduleId][$id] = [
            'id' => $id,
            'label' => (string) ($action['label'] ?? $id),
            'color' => (string) ($action['color'] ?? 'primary'),
            'confirm' => isset($action['confirm']) ? (string) $action['confirm'] : null,
            'outlined' => (bool) ($action['outlined'] ?? false),
            'handler' => $action['handler'],
        ];
    }

    /**
     * @return list<array{id: string, label: string, color: string, confirm: ?string, outlined: bool}>
     */
    public function getAdminActions(string $moduleId): array
    {
        $out = [];
        foreach ($this->adminActions[$moduleId] ?? [] as $action) {
            $out[] = [
                'id' => $action['id'],
                'label' => $action['label'],
                'color' => $action['color'],
                'confirm' => $action['confirm'],
                'outlined' => $action['outlined'],
            ];
        }

        return $out;
    }

    /**
     * @return array{ok: bool, message: string}|null
     */
    public function runAdminAction(string $moduleId, string $actionId): ?array
    {
        $action = $this->adminActions[$moduleId][$actionId] ?? null;
        if (! $action || ! is_callable($action['handler'])) {
            return null;
        }

        $result = ($action['handler'])();
        if (is_array($result)) {
            return [
                'ok' => (bool) ($result['ok'] ?? true),
                'message' => (string) ($result['message'] ?? ''),
            ];
        }

        return ['ok' => true, 'message' => is_string($result) ? $result : ''];
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function isEnabled(string $name): bool
    {
        if (! Schema::hasTable('system_modules')) {
            return false;
        }

        $row = SystemModule::query()->where('name', $name)->first();

        return $row ? (bool) $row->enabled : false;
    }

    /**
     * @return array<string, array{name: string, enabled: bool, path: string, version?: string, description?: string, id?: string}>
     */
    public function discover(): array
    {
        $out = [];
        if (! File::isDirectory($this->basePath)) {
            return $out;
        }

        foreach (File::directories($this->basePath) as $dir) {
            $key = basename($dir);
            $manifest = $this->getManifest($key);
            $out[$key] = [
                'id' => $manifest['id'] ?? $key,
                'name' => $manifest['name'] ?? $key,
                'enabled' => $this->isEnabled($key),
                'path' => $dir,
                'version' => $manifest['version'] ?? null,
                'description' => $manifest['description'] ?? null,
            ];
        }

        return $out;
    }

    public function loadEnabled(): void
    {
        if (! Schema::hasTable('system_modules')) {
            return;
        }

        SystemModule::query()
            ->where('enabled', true)
            ->get()
            ->each(fn (SystemModule $row) => $this->load($row->name));
    }

    public function load(string $name): void
    {
        if (isset($this->loaded[$name])) {
            return;
        }

        $path = $this->basePath.DIRECTORY_SEPARATOR.$name;
        if (! File::isDirectory($path)) {
            return;
        }

        $providerPath = $path.DIRECTORY_SEPARATOR.'SystemModuleServiceProvider.php';
        if (File::exists($providerPath)) {
            try {
                $providerClass = require $providerPath;
                if (is_string($providerClass) && class_exists($providerClass)) {
                    app()->register($providerClass);
                }
            } catch (\Throwable $e) {
                report($e);

                return;
            }
        }

        $this->loaded[$name] = true;
    }

    /**
     * @return array{id?: string, name?: string, version?: string, description?: string}
     */
    public function getManifest(string $name): array
    {
        $path = $this->basePath.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'system-module.json';
        if (! File::exists($path)) {
            return [];
        }

        $json = json_decode(File::get($path), true);
        if (! is_array($json)) {
            return [];
        }

        $out = [];
        foreach (['id', 'name', 'version', 'description'] as $field) {
            if (isset($json[$field]) && is_string($json[$field])) {
                $out[$field] = $json[$field];
            }
        }

        return $out;
    }
}

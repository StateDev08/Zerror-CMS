<?php

namespace App\Support;

use App\Models\Module;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\SystemModule;
use App\Models\WidgetInstance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Einheitlicher ZIP-Installer für Module, Plugins, Widgets und Themes.
 */
class PackageInstaller
{
    public const TYPE_MODULE = 'module';

    public const TYPE_PLUGIN = 'plugin';

    public const TYPE_WIDGET = 'widget';

    public const TYPE_THEME = 'theme';

    public const TYPE_SYSTEM_MODULE = 'system-module';

    public const MAX_ZIP_BYTES = 40 * 1024 * 1024;

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_MODULE,
        self::TYPE_PLUGIN,
        self::TYPE_WIDGET,
        self::TYPE_THEME,
        self::TYPE_SYSTEM_MODULE,
    ];

    /** @var list<string> */
    private const ALLOWED_EXTENSIONS = [
        'json', 'md', 'txt', 'php', 'blade.php', 'css', 'js', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'otf', 'eot',
    ];

    /** @var list<string> */
    private const BLOCKED_BASENAMES = [
        '.htaccess', '.user.ini', 'web.config', 'index.php', 'artisan',
    ];

    public function modulesPath(): string
    {
        return base_path('modules');
    }

    public function pluginsPath(): string
    {
        return base_path('plugins');
    }

    public function systemModulesPath(): string
    {
        return base_path('system-modules');
    }

    public function widgetsPath(): string
    {
        return base_path('widgets');
    }

    /**
     * @return array{ok: bool, message: string, name?: string, type?: string, overwritten?: bool}
     */
    public function installFromUpload(UploadedFile $file, ?string $type = null, bool $overwrite = false, bool $enable = true): array
    {
        if ($type !== null && ! in_array($type, self::TYPES, true)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.type_invalid')];
        }

        if (! class_exists(ZipArchive::class)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_ext_missing')];
        }

        if (! $file->isValid()) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_invalid_upload')];
        }

        $size = (int) $file->getSize();
        $maxKb = min(UploadLimits::fileKb(), (int) floor(self::MAX_ZIP_BYTES / 1024));
        if ($size <= 0 || $size > $maxKb * 1024) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_too_large', ['mb' => (int) ceil($maxKb / 1024)])];
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getMimeType();
        if ($ext !== 'zip' && ! in_array($mime, ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'], true)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_type_invalid')];
        }

        // Themes: bestehender ThemeInstaller (strengere Regeln)
        if ($type === self::TYPE_THEME) {
            $result = app(ThemeInstaller::class)->installFromUpload($file, $overwrite);
            if ($result['ok'] ?? false) {
                $result['type'] = self::TYPE_THEME;
                $result['name'] = $result['theme'] ?? ($result['name'] ?? null);
            }

            return $result;
        }

        $tmpRoot = storage_path('app/package-install/'.Str::lower(Str::random(16)));
        File::ensureDirectoryExists($tmpRoot);

        try {
            $zipPath = $tmpRoot.'/upload.zip';
            if (! $this->storeUploadedZip($file, $zipPath)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_store_failed')];
            }

            $extractPath = $tmpRoot.'/extracted';
            File::ensureDirectoryExists($extractPath);

            $zip = new ZipArchive;
            $opened = $zip->open($zipPath);
            if ($opened !== true) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_open_failed', ['code' => (string) $opened])];
            }

            $scan = $this->scanZip($zip, $type);
            if (! $scan['ok']) {
                $zip->close();

                return $scan;
            }

            if (! $zip->extractTo($extractPath)) {
                $zip->close();

                return ['ok' => false, 'message' => __('zerrocms.packages.zip_extract_failed')];
            }
            $zip->close();

            $detected = $this->detectPackage($extractPath, $type);
            if ($detected === null) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_type_unknown')];
            }

            [$resolvedType, $package, $manifestFile] = $detected;

            // Theme nach Auto-Detect → ThemeInstaller-Pfad nicht mehr (ZIP schon moved). Manuell kopieren wie Package.
            if ($resolvedType === self::TYPE_THEME) {
                return $this->installThemePackage($package, $overwrite);
            }

            $manifest = $this->readManifest($package.DIRECTORY_SEPARATOR.$manifestFile);
            if ($manifest === null) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_manifest_invalid')];
            }

            $folderKey = $this->resolveFolderKey($manifest, $resolvedType);
            if ($folderKey === null) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_name_invalid')];
            }

            if ($resolvedType === self::TYPE_PLUGIN) {
                $hasLocal = is_file($package.DIRECTORY_SEPARATOR.'PluginServiceProvider.php');
                if (! $hasLocal && trim((string) ($manifest['provider'] ?? '')) === '') {
                    return ['ok' => false, 'message' => __('zerrocms.packages.plugin_provider_missing')];
                }
            }

            if ($resolvedType === self::TYPE_WIDGET && ! is_file($package.DIRECTORY_SEPARATOR.'Widget.php')) {
                return ['ok' => false, 'message' => __('zerrocms.packages.widget_php_missing')];
            }

            if ($resolvedType === self::TYPE_SYSTEM_MODULE
                && ! is_file($package.DIRECTORY_SEPARATOR.'SystemModuleServiceProvider.php')) {
                return ['ok' => false, 'message' => __('zerrocms.packages.system_module_provider_missing')];
            }

            $fileCheck = $this->validateExtractedFiles($package);
            if (! $fileCheck['ok']) {
                return $fileCheck;
            }

            $manifest = $this->normalizeManifest($manifest, $resolvedType, $folderKey);
            File::put(
                $package.DIRECTORY_SEPARATOR.$manifestFile,
                json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
            );

            $targetRoot = match ($resolvedType) {
                self::TYPE_MODULE => $this->modulesPath(),
                self::TYPE_PLUGIN => $this->pluginsPath(),
                self::TYPE_WIDGET => $this->widgetsPath(),
                self::TYPE_SYSTEM_MODULE => $this->systemModulesPath(),
                default => null,
            };
            if ($targetRoot === null) {
                return ['ok' => false, 'message' => __('zerrocms.packages.type_invalid')];
            }

            $target = $targetRoot.DIRECTORY_SEPARATOR.$folderKey;
            $exists = is_dir($target);
            if ($exists && ! $overwrite) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_exists', ['name' => $folderKey])];
            }

            File::ensureDirectoryExists($targetRoot);
            if ($exists) {
                File::deleteDirectory($target);
            }
            if (! File::copyDirectory($package, $target)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_copy_failed')];
            }

            $this->registerInDatabase($resolvedType, $folderKey, $enable, $manifest);

            return [
                'ok' => true,
                'message' => $exists
                    ? __('zerrocms.packages.zip_updated', ['name' => $folderKey])
                    : __('zerrocms.packages.zip_installed', ['name' => $folderKey]),
                'name' => $folderKey,
                'type' => $resolvedType,
                'overwritten' => $exists,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_failed', ['error' => $e->getMessage()])];
        } finally {
            if (is_dir($tmpRoot)) {
                File::deleteDirectory($tmpRoot);
            }
        }
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function uninstall(string $type, string $name): array
    {
        if ($type === self::TYPE_THEME) {
            return app(ThemeInstaller::class)->deleteTheme($name);
        }

        if (! in_array($type, [self::TYPE_MODULE, self::TYPE_PLUGIN, self::TYPE_WIDGET, self::TYPE_SYSTEM_MODULE], true)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.type_invalid')];
        }

        $key = $this->normalizePackageKey($name);
        if ($key === null) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_name_invalid')];
        }

        $root = match ($type) {
            self::TYPE_MODULE => $this->modulesPath(),
            self::TYPE_PLUGIN => $this->pluginsPath(),
            self::TYPE_WIDGET => $this->widgetsPath(),
            self::TYPE_SYSTEM_MODULE => $this->systemModulesPath(),
            default => '',
        };
        $target = $root.DIRECTORY_SEPARATOR.$key;
        if (! is_dir($target)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.delete_missing', ['name' => $key])];
        }

        try {
            if (! File::deleteDirectory($target) || is_dir($target)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.delete_failed_permissions', ['name' => $key])];
            }
        } catch (\Throwable $e) {
            report($e);

            return ['ok' => false, 'message' => __('zerrocms.packages.delete_failed_permissions', ['name' => $key])];
        }

        if ($type === self::TYPE_MODULE) {
            Module::where('name', $key)->delete();
            Setting::where('key', 'module_'.$key.'_config')->delete();
        } elseif ($type === self::TYPE_PLUGIN) {
            Plugin::where('name', $key)->delete();
            Setting::where('key', 'plugin_'.$key.'_config')->delete();
        } elseif ($type === self::TYPE_SYSTEM_MODULE) {
            SystemModule::where('name', $key)->delete();
            Setting::where('key', 'system_module_'.$key.'_config')->delete();
        }

        if (Schema::hasTable('widget_instances')) {
            WidgetInstance::query()
                ->where(function ($q) use ($key): void {
                    $q->where('widget_key', $key)->orWhere('widget_key', 'like', $key.'_%');
                })
                ->delete();
        }

        return ['ok' => true, 'message' => __('zerrocms.packages.deleted', ['name' => $key])];
    }

    /**
     * @return array{ok: bool, path?: string, filename?: string, message?: string}
     */
    public function buildExampleZip(string $type): array
    {
        if ($type === self::TYPE_THEME) {
            return app(ThemeInstaller::class)->buildExampleZip();
        }

        if (! class_exists(ZipArchive::class)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_ext_missing')];
        }

        $source = match ($type) {
            self::TYPE_MODULE => resource_path('module-skeletons/example'),
            self::TYPE_PLUGIN => resource_path('plugin-skeletons/example'),
            self::TYPE_WIDGET => resource_path('widget-skeletons/example'),
            self::TYPE_SYSTEM_MODULE => resource_path('system-module-skeletons/example'),
            default => null,
        };
        $manifest = match ($type) {
            self::TYPE_MODULE => 'module.json',
            self::TYPE_PLUGIN => 'plugin.json',
            self::TYPE_WIDGET => 'widget.json',
            self::TYPE_SYSTEM_MODULE => 'system-module.json',
            default => null,
        };
        if ($source === null || $manifest === null || ! is_dir($source) || ! is_file($source.DIRECTORY_SEPARATOR.$manifest)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.example_missing')];
        }

        File::ensureDirectoryExists(storage_path('app/package-exports'));
        $filename = 'zerrocms-example-'.$type.'.zip';
        $path = storage_path('app/package-exports/'.$filename);
        if (is_file($path)) {
            @unlink($path);
        }

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['ok' => false, 'message' => __('zerrocms.packages.example_build_failed')];
        }
        $this->addDirectoryToZip($zip, $source, 'example');
        $zip->close();

        return is_file($path)
            ? ['ok' => true, 'path' => $path, 'filename' => $filename]
            : ['ok' => false, 'message' => __('zerrocms.packages.example_build_failed')];
    }

    /**
     * @return array{0: string, 1: string, 2: string}|null [type, packagePath, manifestFile]
     */
    private function detectPackage(string $extractPath, ?string $forcedType): ?array
    {
        $map = [
            self::TYPE_MODULE => 'module.json',
            self::TYPE_PLUGIN => 'plugin.json',
            self::TYPE_WIDGET => 'widget.json',
            self::TYPE_THEME => 'theme.json',
            self::TYPE_SYSTEM_MODULE => 'system-module.json',
        ];

        if ($forcedType !== null) {
            $file = $map[$forcedType] ?? null;
            if ($file === null) {
                return null;
            }
            $root = $this->locatePackageRoot($extractPath, $file);

            return $root ? [$forcedType, $root, $file] : null;
        }

        $hits = [];
        foreach ($map as $type => $file) {
            $root = $this->locatePackageRoot($extractPath, $file);
            if ($root !== null) {
                $hits[] = [$type, $root, $file];
            }
        }

        return count($hits) === 1 ? $hits[0] : null;
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @return array<string, mixed>
     */
    private function normalizeManifest(array $manifest, string $type, string $folderKey): array
    {
        $rawName = trim((string) ($manifest['name'] ?? ''));

        if ($type === self::TYPE_PLUGIN) {
            $manifest['name'] = $folderKey;
            if (empty($manifest['label'])) {
                $manifest['label'] = ($rawName !== '' && $this->normalizePackageKey($rawName) === null)
                    ? $rawName
                    : Str::headline(str_replace('_', ' ', $folderKey));
            }
        } elseif ($type === self::TYPE_MODULE || $type === self::TYPE_WIDGET || $type === self::TYPE_SYSTEM_MODULE) {
            $manifest['id'] = $folderKey;
            if ($rawName === '' || $this->normalizePackageKey($rawName) === $folderKey) {
                $manifest['name'] = Str::headline(str_replace('_', ' ', $folderKey));
            }
        }

        if (empty($manifest['version'])) {
            $manifest['version'] = '1.0.0';
        }

        return $manifest;
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    private function resolveFolderKey(array $manifest, string $type): ?string
    {
        $rawName = trim((string) ($manifest['name'] ?? ''));
        $folderKey = $this->normalizePackageKey((string) ($manifest['id'] ?? $manifest['key'] ?? ''));
        if ($folderKey === null) {
            $folderKey = $this->normalizePackageKey($rawName);
        }
        if ($folderKey === null && $rawName !== '') {
            $folderKey = $this->normalizePackageKey(Str::slug($rawName, '_'));
        }

        return $folderKey;
    }

    /**
     * @return array{ok: bool, message: string, name?: string, type?: string, overwritten?: bool, theme?: string}
     */
    private function installThemePackage(string $package, bool $overwrite): array
    {
        $manifest = $this->readManifest($package.DIRECTORY_SEPARATOR.'theme.json');
        if ($manifest === null) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_manifest_invalid')];
        }

        $name = Str::lower(trim((string) ($manifest['name'] ?? '')));
        if ($name === '' || ! preg_match('/^[a-z][a-z0-9-]{1,40}$/', $name)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_name_invalid')];
        }

        if (in_array($name, ThemeInstaller::RESERVED_NAMES, true)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_name_reserved', ['name' => $name])];
        }

        if (! is_dir($package.DIRECTORY_SEPARATOR.'views')) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_views_missing')];
        }

        $target = base_path('themes').DIRECTORY_SEPARATOR.$name;
        $exists = is_dir($target);
        if ($exists && ! $overwrite) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_exists', ['name' => $name])];
        }

        if ($exists) {
            File::deleteDirectory($target);
        }
        File::ensureDirectoryExists(dirname($target));
        if (! File::copyDirectory($package, $target)) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_copy_failed')];
        }

        $manifest['name'] = $name;
        File::put(
            $target.DIRECTORY_SEPARATOR.'theme.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
        );

        return [
            'ok' => true,
            'message' => $exists
                ? __('zerrocms.packages.zip_updated', ['name' => $name])
                : __('zerrocms.packages.zip_installed', ['name' => $name]),
            'name' => $name,
            'theme' => $name,
            'type' => self::TYPE_THEME,
            'overwritten' => $exists,
        ];
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    private function registerInDatabase(string $type, string $key, bool $enable, array $manifest): void
    {
        if ($type === self::TYPE_MODULE) {
            Module::updateOrCreate(['name' => $key], ['enabled' => $enable]);
            $this->seedConfigDefaults('module', $key, $this->modulesPath().DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.'config.json');

            return;
        }

        if ($type === self::TYPE_SYSTEM_MODULE) {
            SystemModule::updateOrCreate(['name' => $key], ['enabled' => $enable]);

            return;
        }

        if ($type === self::TYPE_PLUGIN) {
            $order = (int) (Plugin::max('order') ?? 0) + 1;
            Plugin::updateOrCreate(['name' => $key], ['enabled' => $enable, 'order' => $order]);
            $schema = $manifest['configSchema'] ?? [];
            if (is_array($schema) && empty(plugin_config($key))) {
                $defaults = [];
                foreach ($schema as $fieldKey => $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $k = (string) ($item['key'] ?? (is_string($fieldKey) ? $fieldKey : ''));
                    if ($k !== '' && array_key_exists('default', $item)) {
                        $defaults[$k] = $item['default'];
                    }
                }
                if ($defaults !== []) {
                    set_plugin_config($key, $defaults);
                }
            }
        }
        // Widgets: kein DB-Eintrag nötig – Ordner unter widgets/ reicht
    }

    /**
     * Livewire-Temp-Uploads sind keine PHP-HTTP-Uploads → move_uploaded_file() scheitert.
     * Deshalb immer per copy speichern.
     */
    private function storeUploadedZip(UploadedFile $file, string $zipPath): bool
    {
        $source = $file->getRealPath();
        if ($source === false || $source === '' || ! is_readable($source)) {
            return false;
        }

        File::ensureDirectoryExists(dirname($zipPath));

        if (is_file($zipPath)) {
            @unlink($zipPath);
        }

        if (! @copy($source, $zipPath)) {
            // Fallback: Stream lesen
            $contents = @file_get_contents($source);
            if ($contents === false || $contents === '') {
                return false;
            }
            if (@file_put_contents($zipPath, $contents) === false) {
                return false;
            }
        }

        @chmod($zipPath, 0644);

        return is_file($zipPath) && filesize($zipPath) > 0;
    }

    private function seedConfigDefaults(string $kind, string $key, string $schemaPath): void
    {
        if ($kind !== 'module' || ! is_file($schemaPath) || ! empty(module_config($key))) {
            return;
        }
        $schema = json_decode((string) file_get_contents($schemaPath), true);
        if (! is_array($schema)) {
            return;
        }
        $defaults = [];
        foreach ($schema as $fieldKey => $item) {
            if (! is_array($item)) {
                continue;
            }
            $k = (string) ($item['key'] ?? (is_string($fieldKey) ? $fieldKey : ''));
            if ($k !== '' && array_key_exists('default', $item)) {
                $defaults[$k] = $item['default'];
            }
        }
        if ($defaults !== []) {
            set_module_config($key, $defaults);
        }
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    private function scanZip(ZipArchive $zip, ?string $type): array
    {
        $maxFiles = $type === self::TYPE_THEME ? 2000 : 500;
        if ($zip->numFiles <= 0 || $zip->numFiles > $maxFiles) {
            return ['ok' => false, 'message' => __('zerrocms.packages.zip_file_count')];
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (! is_array($stat)) {
                continue;
            }
            $name = str_replace('\\', '/', (string) ($stat['name'] ?? ''));
            if ($name === '' || str_ends_with($name, '/')) {
                continue;
            }
            if (str_contains($name, '..') || str_starts_with($name, '/') || preg_match('#^[A-Za-z]:#', $name)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_path_unsafe')];
            }
            $base = basename($name);
            if (in_array(strtolower($base), self::BLOCKED_BASENAMES, true)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_blocked_file', ['file' => $base])];
            }
            if (! $this->isAllowedFileName($base)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_blocked_ext', ['file' => $base])];
            }
        }

        return ['ok' => true];
    }

    private function locatePackageRoot(string $extractPath, string $manifestFile): ?string
    {
        if (is_file($extractPath.DIRECTORY_SEPARATOR.$manifestFile)) {
            return $extractPath;
        }

        $dirs = array_values(array_filter(File::directories($extractPath), function ($dir) {
            return ! str_starts_with(basename($dir), '__') && basename($dir) !== '.';
        }));

        $hits = [];
        foreach ($dirs as $dir) {
            if (is_file($dir.DIRECTORY_SEPARATOR.$manifestFile)) {
                $hits[] = $dir;
            }
        }

        return count($hits) === 1 ? $hits[0] : null;
    }

    /** @return array<string, mixed>|null */
    private function readManifest(string $path): ?array
    {
        $raw = @file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return null;
        }
        $data = json_decode($raw, true);

        return is_array($data) ? $data : null;
    }

    private function normalizePackageKey(string $name): ?string
    {
        $name = Str::lower(trim($name));
        $name = str_replace('-', '_', $name);
        if ($name === '' || ! preg_match('/^[a-z][a-z0-9_]{1,40}$/', $name)) {
            return null;
        }
        if (str_contains($name, '__') || str_ends_with($name, '_')) {
            return null;
        }

        return $name;
    }

    /** @return array{ok: bool, message?: string} */
    private function validateExtractedFiles(string $package): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($package, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (! $file->isFile()) {
                continue;
            }
            $base = $file->getFilename();
            if (in_array(strtolower($base), self::BLOCKED_BASENAMES, true)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_blocked_file', ['file' => $base])];
            }
            if (! $this->isAllowedFileName($base)) {
                return ['ok' => false, 'message' => __('zerrocms.packages.zip_blocked_ext', ['file' => $base])];
            }
        }

        return ['ok' => true];
    }

    private function isAllowedFileName(string $basename): bool
    {
        $lower = strtolower($basename);
        if (str_starts_with($lower, '.')) {
            return false;
        }
        foreach (self::ALLOWED_EXTENSIONS as $ext) {
            if (str_ends_with($lower, '.'.$ext)) {
                return true;
            }
        }

        return false;
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $prefix): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (! $file->isFile()) {
                continue;
            }
            $absolute = $file->getPathname();
            $relative = $prefix.'/'.ltrim(Str::of($absolute)
                ->replace('\\', '/')
                ->after(Str::of($dir)->replace('\\', '/')->finish('/'))
                ->toString(), '/');
            $zip->addFile($absolute, $relative);
        }
    }
}

<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Professionelle Theme-Installation aus ZIP (ACP).
 */
class ThemeInstaller
{
    public const MAX_ZIP_BYTES = 40 * 1024 * 1024; // 40 MB hartes Sicherheitslimit

    /** @var list<string> */
    public const RESERVED_NAMES = ['common'];

    /** @var list<string> */
    public const BUILTIN_NAMES = [
        'common', 'minecraft', 'pax-dei', 'seven-days', 'palworld', 'satisfactory',
    ];

    /** @var list<string> */
    private const ALLOWED_EXTENSIONS = [
        'json', 'md', 'txt', 'blade.php', 'php', 'css', 'js', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'otf', 'eot',
    ];

    /** @var list<string> */
    private const BLOCKED_BASENAMES = [
        '.htaccess', '.user.ini', 'web.config', 'index.php', 'artisan',
    ];

    public function themesPath(): string
    {
        return base_path('themes');
    }

    public function skeletonPath(): string
    {
        return resource_path('theme-skeletons/example');
    }

    /**
     * @return array{ok: bool, message: string, theme?: string, overwritten?: bool}
     */
    public function installFromUpload(UploadedFile $file, bool $overwrite = false): array
    {
        if (! class_exists(ZipArchive::class)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_ext_missing')];
        }

        if (! $file->isValid()) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_invalid_upload')];
        }

        $size = (int) $file->getSize();
        $maxKb = min(UploadLimits::fileKb(), (int) floor(self::MAX_ZIP_BYTES / 1024));
        if ($size <= 0 || $size > $maxKb * 1024) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_too_large', ['mb' => (int) ceil($maxKb / 1024)])];
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getMimeType();
        if ($ext !== 'zip' && ! in_array($mime, ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'], true)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_type_invalid')];
        }

        $tmpRoot = storage_path('app/theme-install/'.Str::lower(Str::random(16)));
        File::ensureDirectoryExists($tmpRoot);

        try {
            $zipPath = $tmpRoot.'/upload.zip';
            if (! $this->storeUploadedZip($file, $zipPath)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_store_failed')];
            }

            $extractPath = $tmpRoot.'/extracted';
            File::ensureDirectoryExists($extractPath);

            $zip = new ZipArchive;
            $opened = $zip->open($zipPath);
            if ($opened !== true) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_open_failed', ['code' => (string) $opened])];
            }

            $scan = $this->scanZip($zip);
            if (! $scan['ok']) {
                $zip->close();

                return $scan;
            }

            if (! $zip->extractTo($extractPath)) {
                $zip->close();

                return ['ok' => false, 'message' => __('zerrocms.themes.zip_extract_failed')];
            }
            $zip->close();

            $package = $this->locatePackageRoot($extractPath);
            if ($package === null) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_no_manifest')];
            }

            $manifest = $this->readManifest($package.'/theme.json');
            if ($manifest === null) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_manifest_invalid')];
            }

            $name = $this->normalizeThemeName((string) ($manifest['name'] ?? ''));
            if ($name === null) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_name_invalid')];
            }

            if (in_array($name, self::RESERVED_NAMES, true)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_name_reserved', ['name' => $name])];
            }

            if (! is_dir($package.'/views')) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_views_missing')];
            }

            $fileCheck = $this->validateExtractedFiles($package);
            if (! $fileCheck['ok']) {
                return $fileCheck;
            }

            // Manifest-Name erzwingen (Ordner = ID)
            $manifest['name'] = $name;
            $manifest['selectable'] = array_key_exists('selectable', $manifest)
                ? (bool) $manifest['selectable']
                : true;
            if (empty($manifest['parent'])) {
                $manifest['parent'] = 'common';
            }
            if (empty($manifest['version'])) {
                $manifest['version'] = '1.0.0';
            }

            $target = $this->themesPath().DIRECTORY_SEPARATOR.$name;
            $exists = is_dir($target);
            if ($exists && ! $overwrite) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_exists', ['name' => $name])];
            }

            if ($exists) {
                File::deleteDirectory($target);
            }

            File::ensureDirectoryExists(dirname($target));
            if (! File::copyDirectory($package, $target)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_copy_failed')];
            }

            File::put(
                $target.DIRECTORY_SEPARATOR.'theme.json',
                json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
            );

            return [
                'ok' => true,
                'message' => $exists
                    ? __('zerrocms.themes.zip_updated', ['name' => $name])
                    : __('zerrocms.themes.zip_installed', ['name' => $name]),
                'theme' => $name,
                'overwritten' => $exists,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_failed', ['error' => $e->getMessage()])];
        } finally {
            if (is_dir($tmpRoot)) {
                File::deleteDirectory($tmpRoot);
            }
        }
    }

    /**
     * Example-Theme als ZIP-Stream erzeugen.
     *
     * @return array{ok: bool, path?: string, filename?: string, message?: string}
     */
    public function buildExampleZip(): array
    {
        if (! class_exists(ZipArchive::class)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_ext_missing')];
        }

        $source = $this->skeletonPath();
        if (! is_dir($source) || ! is_file($source.'/theme.json')) {
            return ['ok' => false, 'message' => __('zerrocms.themes.example_missing')];
        }

        File::ensureDirectoryExists(storage_path('app/theme-exports'));
        $filename = 'zerrocms-example-theme.zip';
        $path = storage_path('app/theme-exports/'.$filename);
        if (is_file($path)) {
            @unlink($path);
        }

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['ok' => false, 'message' => __('zerrocms.themes.example_build_failed')];
        }

        $rootName = 'example';
        $this->addDirectoryToZip($zip, $source, $rootName);
        $zip->close();

        if (! is_file($path)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.example_build_failed')];
        }

        return ['ok' => true, 'path' => $path, 'filename' => $filename];
    }

    public function isBuiltin(string $name): bool
    {
        return in_array($name, self::BUILTIN_NAMES, true);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function deleteTheme(string $name): array
    {
        $name = $this->normalizeThemeName($name) ?? '';
        if ($name === '' || $this->isBuiltin($name) || in_array($name, self::RESERVED_NAMES, true)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.delete_forbidden')];
        }

        $target = $this->themesPath().DIRECTORY_SEPARATOR.$name;
        if (! is_dir($target)) {
            return ['ok' => false, 'message' => __('zerrocms.themes.delete_missing')];
        }

        $active = app(ThemeManager::class)->active();
        if ($active === $name) {
            return ['ok' => false, 'message' => __('zerrocms.themes.delete_active')];
        }

        File::deleteDirectory($target);

        return ['ok' => true, 'message' => __('zerrocms.themes.deleted', ['name' => $name])];
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    private function scanZip(ZipArchive $zip): array
    {
        if ($zip->numFiles <= 0 || $zip->numFiles > 2000) {
            return ['ok' => false, 'message' => __('zerrocms.themes.zip_file_count')];
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
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_path_unsafe')];
            }
            $base = basename($name);
            if (in_array(strtolower($base), self::BLOCKED_BASENAMES, true)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_blocked_file', ['file' => $base])];
            }
            if (! $this->isAllowedFileName($base)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_blocked_ext', ['file' => $base])];
            }
            // PHP nur unter views/ erlauben (Blade kompiliert als .blade.php)
            if (str_ends_with(strtolower($base), '.php') && ! str_ends_with(strtolower($base), '.blade.php')) {
                if (! str_contains('/'.$name, '/views/')) {
                    return ['ok' => false, 'message' => __('zerrocms.themes.zip_php_forbidden', ['file' => $name])];
                }
            }
        }

        return ['ok' => true];
    }

    private function locatePackageRoot(string $extractPath): ?string
    {
        $direct = $extractPath.DIRECTORY_SEPARATOR.'theme.json';
        if (is_file($direct)) {
            return $extractPath;
        }

        $dirs = array_values(array_filter(File::directories($extractPath), function ($dir) {
            return ! str_starts_with(basename($dir), '__') && basename($dir) !== '.';
        }));

        if (count($dirs) === 1 && is_file($dirs[0].DIRECTORY_SEPARATOR.'theme.json')) {
            return $dirs[0];
        }

        // Mehrere Ordner: genau einen mit theme.json finden
        $hits = [];
        foreach ($dirs as $dir) {
            if (is_file($dir.DIRECTORY_SEPARATOR.'theme.json')) {
                $hits[] = $dir;
            }
        }

        return count($hits) === 1 ? $hits[0] : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readManifest(string $path): ?array
    {
        $raw = @file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return null;
        }
        $data = json_decode($raw, true);

        return is_array($data) ? $data : null;
    }

    private function normalizeThemeName(string $name): ?string
    {
        $name = Str::lower(trim($name));
        if ($name === '' || ! preg_match('/^[a-z][a-z0-9-]{1,40}$/', $name)) {
            return null;
        }
        if (str_contains($name, '--') || str_ends_with($name, '-')) {
            return null;
        }

        return $name;
    }

    /**
     * @return array{ok: bool, message?: string}
     */
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
            $rel = Str::of($file->getPathname())
                ->replace('\\', '/')
                ->after(Str::of($package)->replace('\\', '/')->finish('/'))
                ->toString();

            if (str_contains($rel, '..')) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_path_unsafe')];
            }

            $base = $file->getFilename();
            if (in_array(strtolower($base), self::BLOCKED_BASENAMES, true)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_blocked_file', ['file' => $base])];
            }
            if (! $this->isAllowedFileName($base)) {
                return ['ok' => false, 'message' => __('zerrocms.themes.zip_blocked_ext', ['file' => $base])];
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

    /**
     * Livewire-Temp-Uploads sind keine PHP-HTTP-Uploads → copy statt move().
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
}

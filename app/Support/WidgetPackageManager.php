<?php

namespace App\Support;

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;

/**
 * Lädt installierte Widget-Pakete aus widgets/{id}/.
 */
class WidgetPackageManager
{
    public function widgetsPath(): string
    {
        return base_path('widgets');
    }

    /**
     * @return array<string, array{id: string, name: string, path: string, version?: string, description?: string}>
     */
    public function discover(): array
    {
        $out = [];
        $root = $this->widgetsPath();
        if (! File::isDirectory($root)) {
            return $out;
        }

        foreach (File::directories($root) as $dir) {
            $id = basename($dir);
            $manifestPath = $dir.DIRECTORY_SEPARATOR.'widget.json';
            if (! is_file($manifestPath)) {
                continue;
            }
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            if (! is_array($manifest)) {
                continue;
            }
            $out[$id] = [
                'id' => $id,
                'name' => (string) ($manifest['name'] ?? $id),
                'path' => $dir,
                'version' => isset($manifest['version']) ? (string) $manifest['version'] : null,
                'description' => isset($manifest['description']) ? (string) $manifest['description'] : null,
                'manifest' => $manifest,
            ];
        }

        return $out;
    }

    public function loadAll(): void
    {
        $registry = app(WidgetRegistry::class);

        foreach ($this->discover() as $id => $meta) {
            $widgetFile = $meta['path'].DIRECTORY_SEPARATOR.'Widget.php';
            if (! is_file($widgetFile)) {
                continue;
            }
            try {
                $result = require $widgetFile;
                $widget = null;
                if ($result instanceof WidgetContract) {
                    $widget = $result;
                } elseif (is_string($result) && class_exists($result)) {
                    $instance = new $result;
                    if ($instance instanceof WidgetContract) {
                        $widget = $instance;
                    }
                }
                if ($widget === null) {
                    continue;
                }
                $slots = $meta['manifest']['slots'] ?? ['left', 'right'];
                if (! is_array($slots) || $slots === []) {
                    $slots = ['left', 'right'];
                }
                $registry->register($widget, array_values(array_map('strval', $slots)));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}

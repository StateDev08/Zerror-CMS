<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class Ts3ViewerWidget implements WidgetContract
{
    public function id(): string
    {
        return 'ts3_viewer';
    }

    public function title(): string
    {
        return __('widgets.ts3_viewer_title', [], 'TeamSpeak Viewer');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('ts3_viewer'), $config);
        $serverUrl = $config['server_url'] ?? '';
        $title = $config['title'] ?? 'TeamSpeak Viewer';
        if (empty($serverUrl)) {
            return '<div class="widget widget-ts3-viewer rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'ts3_viewer'], 'Modul konfigurieren.') . '</p></div>';
        }
        return '<div class="widget widget-ts3-viewer rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-2">' . e($title) . '</h3><p class="text-sm text-gray-600 dark:text-gray-400">' . e($serverUrl) . '</p></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

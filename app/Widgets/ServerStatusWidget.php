<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class ServerStatusWidget implements WidgetContract
{
    public function id(): string
    {
        return 'server_status';
    }

    public function title(): string
    {
        return __('widgets.server_status_title', [], 'Server-Status');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('server_status'), $config);
        $host = $config['host'] ?? '';
        $port = (int) ($config['port'] ?? 25565);
        $label = $config['label'] ?? 'Server-Status';
        if (empty($host)) {
            return '<div class="widget widget-server-status rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'server_status'], 'Modul konfigurieren.') . '</p></div>';
        }
        $connected = @fsockopen($host, $port, $errno, $errstr, 2);
        $online = $connected !== false;
        if ($connected) {
            fclose($connected);
        }
        $statusText = $online ? __('widgets.server_online', [], 'Online') : __('widgets.server_offline', [], 'Offline');
        $statusColor = $online ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        return '<div class="widget widget-server-status rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-2">' . e($label) . '</h3><p class="text-sm ' . $statusColor . '">' . $statusText . '</p></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

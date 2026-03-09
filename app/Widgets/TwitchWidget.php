<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class TwitchWidget implements WidgetContract
{
    public function id(): string
    {
        return 'twitch';
    }

    public function title(): string
    {
        return __('widgets.twitch_title', [], 'Twitch');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('twitch'), $config);
        $channel = $config['channel'] ?? '';
        $buttonText = $config['button_text'] ?? 'Auf Twitch ansehen';
        if (empty($channel)) {
            return '<div class="widget widget-twitch rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'twitch'], 'Modul Twitch konfigurieren.') . '</p></div>';
        }
        $url = 'https://twitch.tv/' . e($channel);
        return '<div class="widget widget-twitch rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">Twitch</h3><a href="' . $url . '" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#9146FF] hover:bg-[#7c3aed] text-white text-sm font-medium">' . e($buttonText) . '</a></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

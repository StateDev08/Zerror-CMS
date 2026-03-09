<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class TeamspeakWidget implements WidgetContract
{
    public function id(): string
    {
        return 'teamspeak';
    }

    public function title(): string
    {
        return __('widgets.teamspeak_title', [], 'TeamSpeak');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('teamspeak'), $config);
        $serverUrl = $config['server_url'] ?? '';
        $port = $config['port'] ?? 9987;
        $buttonText = $config['button_text'] ?? 'TeamSpeak beitreten';
        if (empty($serverUrl)) {
            return '<div class="widget widget-teamspeak rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'teamspeak'], 'Modul TeamSpeak konfigurieren.') . '</p></div>';
        }
        $url = (str_contains($serverUrl, '://') ? $serverUrl : 'ts3server://' . $serverUrl) . '?port=' . (int) $port;
        return view('components.widgets.teamspeak', [
            'url' => $url,
            'buttonText' => $buttonText,
        ])->render();
    }

    public function configSchema(): array
    {
        return [];
    }
}

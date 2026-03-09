<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class VoiceChatWidget implements WidgetContract
{
    public function id(): string
    {
        return 'voice_chat';
    }

    public function title(): string
    {
        return __('widgets.voice_chat_title', [], 'Voice Chat');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('voice_chat'), $config);
        $linkUrl = $config['link_url'] ?? '';
        $title = $config['title'] ?? 'Voice Chat';
        $buttonText = $config['button_text'] ?? 'Beitreten';
        if (empty($linkUrl)) {
            return '<div class="widget widget-voice-chat rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'voice_chat'], 'Modul konfigurieren.') . '</p></div>';
        }
        return '<div class="widget widget-voice-chat rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">' . e($title) . '</h3><a href="' . e($linkUrl) . '" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg theme-bg-primary text-white text-sm font-medium">' . e($buttonText) . '</a></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

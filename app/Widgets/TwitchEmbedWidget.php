<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class TwitchEmbedWidget implements WidgetContract
{
    public function id(): string
    {
        return 'twitch_embed';
    }

    public function title(): string
    {
        return __('widgets.twitch_embed_title', [], 'Twitch Stream');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(plugin_config('twitch_embed'), $config);
        $channel = trim((string) ($config['channel'] ?? ''));
        $showChat = (bool) ($config['show_chat'] ?? false);
        if ($channel === '') {
            return '<div class="widget widget-twitch-embed rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_plugin', [], 'Plugin Twitch-Embed konfigurieren.') . '</p></div>';
        }
        $channel = e($channel);
        $parent = e(request()->getHost());
        $streamUrl = "https://player.twitch.tv/?channel={$channel}&parent={$parent}";
        $html = '<div class="widget widget-twitch-embed rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 overflow-hidden">';
        $html .= '<h3 class="font-semibold text-gray-900 dark:text-white mb-2">Twitch</h3>';
        $html .= '<div class="aspect-video w-full rounded overflow-hidden bg-black">';
        $html .= '<iframe src="' . $streamUrl . '" frameborder="0" allowfullscreen="true" scrolling="no" class="w-full h-full" title="Twitch Stream"></iframe>';
        $html .= '</div>';
        if ($showChat) {
            $chatUrl = "https://www.twitch.tv/embed/{$channel}/chat?parent={$parent}&darkpopout";
            $html .= '<div class="mt-2 h-64 w-full rounded overflow-hidden bg-gray-900">';
            $html .= '<iframe src="' . $chatUrl . '" frameborder="0" scrolling="yes" class="w-full h-full" title="Twitch Chat"></iframe>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array
    {
        return [];
    }
}

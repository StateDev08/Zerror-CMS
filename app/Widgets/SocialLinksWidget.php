<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class SocialLinksWidget implements WidgetContract
{
    public function id(): string
    {
        return 'social_links';
    }

    public function title(): string
    {
        return __('widgets.social_links_title', [], 'Social Links');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('social_links'), $config);
        $title = $config['title'] ?? 'Folge uns';
        $links = array_filter([
            'Twitter' => $config['twitter_url'] ?? '',
            'YouTube' => $config['youtube_url'] ?? '',
            'Twitch' => $config['twitch_url'] ?? '',
        ]);
        if (empty($links)) {
            return '<div class="widget widget-social-links rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'social_links'], 'Modul konfigurieren.') . '</p></div>';
        }
        $html = '<div class="widget widget-social-links rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">' . e($title) . '</h3><div class="flex flex-wrap gap-2">';
        foreach ($links as $label => $url) {
            $html .= '<a href="' . e($url) . '" target="_blank" rel="noopener" class="px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">' . e($label) . '</a>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function configSchema(): array
    {
        return [];
    }
}

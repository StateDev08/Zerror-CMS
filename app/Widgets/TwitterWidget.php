<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class TwitterWidget implements WidgetContract
{
    public function id(): string
    {
        return 'twitter';
    }

    public function title(): string
    {
        return __('widgets.twitter_title', [], 'Twitter/X');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('twitter'), $config);
        $handle = $config['handle'] ?? '';
        $embedUrl = $config['embed_url'] ?? '';
        $url = ! empty($embedUrl) ? $embedUrl : (! empty($handle) ? 'https://twitter.com/' . ltrim($handle, '@') : '');
        if (empty($url)) {
            return '<div class="widget widget-twitter rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'twitter'], 'Modul Twitter konfigurieren.') . '</p></div>';
        }
        return '<div class="widget widget-twitter rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">Twitter/X</h3><a href="' . e($url) . '" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-black dark:bg-gray-700 text-white text-sm font-medium">@' . e(ltrim($handle, '@') ?: 'Folgen') . '</a></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

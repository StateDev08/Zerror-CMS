<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class NewsletterBoxWidget implements WidgetContract
{
    public function id(): string
    {
        return 'newsletter_box';
    }

    public function title(): string
    {
        return __('widgets.newsletter_box_title', [], 'Newsletter');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('newsletter_box'), $config);
        $title = $config['title'] ?? 'Newsletter';
        $targetUrl = $config['target_url'] ?? route('newsletter.index');
        $buttonText = $config['button_text'] ?? 'Anmelden';
        return '<div class="widget widget-newsletter-box rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">' . e($title) . '</h3><a href="' . e($targetUrl) . '" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg theme-bg-primary text-white text-sm font-medium">' . e($buttonText) . '</a></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

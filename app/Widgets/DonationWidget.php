<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class DonationWidget implements WidgetContract
{
    public function id(): string
    {
        return 'donation';
    }

    public function title(): string
    {
        return __('widgets.donation_title');
    }

    public function render(array $config = []): string
    {
        $url = $config['url'] ?? config('clan.donation_url', '');
        $buttonText = $config['button_text'] ?? __('widgets.donation_button');
        if (empty($url)) {
            return '<div class="widget widget-donation rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.donation_configure') . '</p></div>';
        }
        return view('components.widgets.donation', [
            'url' => $url,
            'buttonText' => $buttonText,
        ])->render();
    }

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array
    {
        return [
            'url' => [
                'type' => 'text',
                'label' => __('widgets.donation_url'),
                'default' => '',
            ],
            'button_text' => [
                'type' => 'text',
                'label' => __('widgets.donation_button_text'),
                'default' => __('widgets.donation_button'),
            ],
        ];
    }
}

<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class DiscordWidget implements WidgetContract
{
    public function id(): string
    {
        return 'discord';
    }

    public function title(): string
    {
        return __('widgets.discord_title');
    }

    public function render(array $config = []): string
    {
        $inviteUrl = $config['invite_url'] ?? config('clan.discord_invite_url', '');
        $buttonText = $config['button_text'] ?? __('widgets.discord_join');
        if (empty($inviteUrl)) {
            return '<div class="widget widget-discord rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.discord_configure') . '</p></div>';
        }
        return view('components.widgets.discord', [
            'inviteUrl' => $inviteUrl,
            'buttonText' => $buttonText,
        ])->render();
    }

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array
    {
        return [
            'invite_url' => [
                'type' => 'text',
                'label' => __('widgets.discord_invite_url'),
                'default' => '',
            ],
            'button_text' => [
                'type' => 'text',
                'label' => __('widgets.discord_button_text'),
                'default' => __('widgets.discord_join'),
            ],
        ];
    }
}

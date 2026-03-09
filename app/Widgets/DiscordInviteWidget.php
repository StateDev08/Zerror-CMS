<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class DiscordInviteWidget implements WidgetContract
{
    public function id(): string
    {
        return 'discord_invite';
    }

    public function title(): string
    {
        return __('widgets.discord_invite_title', [], 'Discord einladen');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('discord_invite'), $config);
        $inviteUrl = $config['invite_url'] ?? '';
        $buttonText = $config['button_text'] ?? 'Discord beitreten';
        if (empty($inviteUrl)) {
            return '<div class="widget widget-discord-invite rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><p class="text-sm text-gray-500 dark:text-gray-400">' . __('widgets.configure_in_module', ['module' => 'discord_invite'], 'Modul konfigurieren.') . '</p></div>';
        }
        return '<div class="widget widget-discord-invite rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"><h3 class="font-semibold text-gray-900 dark:text-white mb-3">Discord</h3><a href="' . e($inviteUrl) . '" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#5865F2] hover:bg-[#4752C4] text-white text-sm font-medium">' . e($buttonText) . '</a></div>';
    }

    public function configSchema(): array
    {
        return [];
    }
}

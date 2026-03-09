<?php

namespace App\Filament\Widgets;

use App\Support\PluginManager;
use Filament\Widgets\Widget;

class BackupReminderWidget extends Widget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.backup-reminder-widget';

    public static function canView(): bool
    {
        $pluginManager = app(PluginManager::class);
        if (! $pluginManager->isEnabled('backup_reminder')) {
            return false;
        }
        $config = plugin_config('backup_reminder');

        return (bool) ($config['enabled'] ?? true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $config = plugin_config('backup_reminder');
        $text = (string) ($config['reminder_text'] ?? 'Bitte regelmäßig Backups prüfen.');

        return [
            'reminderText' => $text,
        ];
    }
}

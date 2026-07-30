<?php

namespace App\Filament\Pages;

use App\Support\DatabaseUpdater;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class UpdateDatabasePage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Datenbank aktualisieren';

    protected static ?string $title = 'Datenbank aktualisieren';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.update-database';

    public bool $syncPermissions = true;

    public bool $syncMenus = false;

    public bool $syncLegalPages = true;

    public bool $syncHomeWidgets = false;

    public bool $clearCaches = true;

    /** @var list<array{label: string, ok: bool, detail: string}> */
    public array $lastSteps = [];

    public ?bool $lastOk = null;

    protected static function cmsPagePermission(): string
    {
        return 'manage_settings';
    }

    public function runUpdate(): void
    {
        $result = app(DatabaseUpdater::class)->run([
            'sync_permissions' => $this->syncPermissions,
            'sync_menus' => $this->syncMenus,
            'sync_legal_pages' => $this->syncLegalPages,
            'sync_home_widgets' => $this->syncHomeWidgets,
            'clear_caches' => $this->clearCaches,
        ]);

        $this->lastOk = $result['ok'];
        $this->lastSteps = $result['steps'];

        if ($result['ok']) {
            Notification::make()
                ->title(__('update_db.success'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('update_db.failed'))
                ->danger()
                ->send();
        }
    }
}

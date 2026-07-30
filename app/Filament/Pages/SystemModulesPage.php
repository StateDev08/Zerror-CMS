<?php

namespace App\Filament\Pages;

use App\Models\SystemModule;
use App\Support\PackageInstaller;
use App\Support\SystemModuleManager;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class SystemModulesPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'System-Module';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'System-Module verwalten';

    protected static ?int $navigationSort = 7;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system-modules';

    protected static function cmsPagePermission(): string
    {
        return 'manage_system_modules';
    }

    public function getViewData(): array
    {
        $manager = app(SystemModuleManager::class);
        $items = Schema::hasTable('system_modules') ? $manager->discover() : [];

        foreach ($items as $key => &$item) {
            $item['admin_actions'] = $manager->getAdminActions($key);
        }
        unset($item);

        return [
            'systemModules' => $items,
            'tableMissing' => ! Schema::hasTable('system_modules'),
            'installerUrl' => PackagesInstallerPage::getUrl(),
        ];
    }

    public function runSystemModuleAdminAction(string $name, string $actionId): void
    {
        if (! Schema::hasTable('system_modules')) {
            Notification::make()->title(__('zerrocms.system_modules.table_missing'))->danger()->send();

            return;
        }

        $manager = app(SystemModuleManager::class);
        if (! isset($manager->discover()[$name]) || ! $manager->isEnabled($name)) {
            Notification::make()->title(__('zerrocms.system_modules.action_requires_enabled'))->danger()->send();

            return;
        }

        try {
            $result = $manager->runAdminAction($name, $actionId);
            if ($result === null) {
                Notification::make()->title(__('zerrocms.system_modules.action_unknown'))->danger()->send();

                return;
            }

            Notification::make()
                ->title($result['message'] !== '' ? $result['message'] : __('zerrocms.system_modules.action_done'))
                ->{$result['ok'] ? 'success' : 'danger'}()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.action_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }

    public function toggleSystemModule(string $name): void
    {
        if (! Schema::hasTable('system_modules')) {
            Notification::make()->title(__('zerrocms.system_modules.table_missing'))->danger()->send();

            return;
        }
        if (! isset(app(SystemModuleManager::class)->discover()[$name])) {
            return;
        }
        try {
            $row = SystemModule::firstOrNew(['name' => $name]);
            $row->enabled = ! ($row->exists && (bool) $row->enabled);
            $row->save();
            Notification::make()
                ->title($row->enabled
                    ? __('zerrocms.system_modules.activated')
                    : __('zerrocms.system_modules.deactivated'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.action_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }

    public function uninstallSystemModule(string $name): void
    {
        $result = app(PackageInstaller::class)->uninstall(PackageInstaller::TYPE_SYSTEM_MODULE, $name);
        Notification::make()
            ->title($result['ok'] ? __('zerrocms.packages.delete_ok') : __('zerrocms.packages.delete_failed'))
            ->body($result['message'])
            ->{$result['ok'] ? 'success' : 'danger'}()
            ->send();
    }
}

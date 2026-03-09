<?php

namespace App\Filament\Pages;

use App\Models\Module;
use App\Support\ModuleManager;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class ModulesPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Module';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Module verwalten';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.modules';

    public ?string $configuringModule = null;

    /** @var array<string, mixed> */
    public array $moduleConfigForm = [];

    /** @var array<int, array{key: string, type: string, label: string, default: mixed}> */
    public array $moduleConfigSchema = [];

    public function getViewData(): array
    {
        $manager = app(ModuleManager::class);
        $modules = [];
        if (Schema::hasTable('modules')) {
            $modules = $manager->discover();
        }
        return [
            'modules' => $modules,
            'modulesTableMissing' => ! Schema::hasTable('modules'),
            'configuringModule' => $this->configuringModule,
            'moduleConfigSchema' => $this->moduleConfigSchema,
            'moduleConfigForm' => $this->moduleConfigForm,
        ];
    }

    public function openModuleConfig(string $name): void
    {
        $manager = app(ModuleManager::class);
        $schema = $manager->getConfigSchema($name);
        $current = module_config($name);
        $form = [];
        foreach ($schema as $item) {
            $key = $item['key'] ?? '';
            if ($key !== '') {
                $form[$key] = $current[$key] ?? ($item['default'] ?? '');
            }
        }
        $this->configuringModule = $name;
        $this->moduleConfigSchema = $schema;
        $this->moduleConfigForm = $form;
    }

    public function saveModuleConfig(): void
    {
        if ($this->configuringModule === null) {
            return;
        }
        try {
            set_module_config($this->configuringModule, $this->moduleConfigForm);
            $this->configuringModule = null;
            $this->moduleConfigSchema = [];
            $this->moduleConfigForm = [];
            $this->notification()->success(__('zerrocms.modules.saved'));
            $this->dispatch('refresh-page');
        } catch (\Throwable $e) {
            report($e);
            $this->notification()->danger(__('zerrocms.save_failed', ['message' => $e->getMessage()]));
        }
    }

    public function closeModuleConfig(): void
    {
        $this->configuringModule = null;
        $this->moduleConfigSchema = [];
        $this->moduleConfigForm = [];
    }

    public function toggleModule(string $name): void
    {
        if (! Schema::hasTable('modules')) {
            $this->notification()->danger(__('zerrocms.modules.table_missing'));
            return;
        }
        $discovered = app(ModuleManager::class)->discover();
        if (! isset($discovered[$name])) {
            return;
        }
        try {
            $module = Module::firstOrNew(['name' => $name]);
            $module->enabled = ! $module->enabled;
            $module->save();
            $this->notification()->success($module->enabled ? 'Modul aktiviert.' : 'Modul deaktiviert.');
            $this->dispatch('refresh-page');
        } catch (\Throwable $e) {
            report($e);
            $this->notification()->danger(__('zerrocms.action_failed', ['message' => $e->getMessage()]));
        }
    }
}

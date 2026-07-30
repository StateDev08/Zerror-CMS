<?php

namespace App\Filament\Pages;

use App\Models\Module;
use App\Support\ModuleManager;
use App\Support\PackageInstaller;
use App\Support\SiteMedia;
use App\Support\UploadLimits;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ModulesPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;
    use WithFileUploads;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Module';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Module verwalten';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.modules';

    protected static function cmsPagePermission(): string
    {
        return 'manage_modules';
    }

    public ?string $configuringModule = null;

    /** @var array<string, mixed> */
    public array $moduleConfigForm = [];

    /** @var array<string, mixed> Temporary uploads keyed like config fields */
    public array $moduleFileUploads = [];

    /** @var array<int, array{key: string, type: string, label: string, default: mixed}> */
    public array $moduleConfigSchema = [];

    public function getViewData(): array
    {
        $manager = app(ModuleManager::class);
        $modules = Schema::hasTable('modules') ? $manager->discover() : [];

        return [
            'modules' => $modules,
            'modulesTableMissing' => ! Schema::hasTable('modules'),
            'configuringModule' => $this->configuringModule,
            'moduleConfigSchema' => $this->moduleConfigSchema,
            'moduleConfigForm' => $this->moduleConfigForm,
            'installerUrl' => PackagesInstallerPage::getUrl(),
            'uploadMaxFileMb' => UploadLimits::fileMb(),
        ];
    }

    public function uninstallModule(string $name): void
    {
        $result = app(PackageInstaller::class)->uninstall(PackageInstaller::TYPE_MODULE, $name);
        Notification::make()
            ->title($result['ok'] ? __('zerrocms.packages.delete_ok') : __('zerrocms.packages.delete_failed'))
            ->body($result['message'])
            ->{$result['ok'] ? 'success' : 'danger'}()
            ->send();
        if ($result['ok'] && $this->configuringModule === $name) {
            $this->closeModuleConfig();
        }
    }

    public function openModuleConfig(string $name): void
    {
        $schema = app(ModuleManager::class)->getConfigSchema($name);
        $current = module_config($name);
        $form = [];
        foreach ($schema as $item) {
            $key = $item['key'] ?? '';
            if ($key === '') {
                continue;
            }
            $type = (string) ($item['type'] ?? 'text');
            $default = $item['default'] ?? '';
            $value = $current[$key] ?? $default;
            if ($type === 'boolean' || $type === 'checkbox') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            $form[$key] = $value;
        }
        $this->configuringModule = $name;
        $this->moduleConfigSchema = $schema;
        $this->moduleConfigForm = $form;
        $this->moduleFileUploads = [];
    }

    public function clearModuleFile(string $key): void
    {
        if ($this->configuringModule === null || $key === '') {
            return;
        }
        $current = $this->moduleConfigForm[$key] ?? null;
        if (is_string($current) && $current !== '') {
            SiteMedia::deleteStoredPath($current);
        }
        $this->moduleConfigForm[$key] = '';
        unset($this->moduleFileUploads[$key]);
    }

    public function saveModuleConfig(): void
    {
        if ($this->configuringModule === null) {
            return;
        }
        try {
            $data = $this->moduleConfigForm;
            $existing = module_config($this->configuringModule);

            foreach ($this->moduleConfigSchema as $item) {
                $key = (string) ($item['key'] ?? '');
                if ($key === '') {
                    continue;
                }
                $type = (string) ($item['type'] ?? 'text');
                if ($type === 'boolean' || $type === 'checkbox') {
                    $data[$key] = filter_var($data[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
                    continue;
                }
                if ($type === 'number') {
                    $data[$key] = is_numeric($data[$key] ?? null) ? 0 + $data[$key] : ($item['default'] ?? 0);
                    continue;
                }
                if ($type === 'file' || $type === 'audio') {
                    $upload = $this->moduleFileUploads[$key] ?? null;
                    if ($upload instanceof TemporaryUploadedFile) {
                        $old = $existing[$key] ?? null;
                        if (is_string($old) && $old !== '') {
                            SiteMedia::deleteStoredPath($old);
                        }
                        $data[$key] = $upload->store('modules/'.$this->configuringModule, 'public');
                    } else {
                        $kept = $data[$key] ?? ($existing[$key] ?? '');
                        $data[$key] = is_string($kept) ? $kept : '';
                    }
                }
            }

            foreach ($data as $k => $v) {
                if ($v instanceof TemporaryUploadedFile) {
                    unset($data[$k]);
                }
            }

            set_module_config($this->configuringModule, $data);
            $this->closeModuleConfig();
            Notification::make()->title(__('zerrocms.modules.saved'))->success()->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.save_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }

    public function closeModuleConfig(): void
    {
        $this->configuringModule = null;
        $this->moduleConfigSchema = [];
        $this->moduleConfigForm = [];
        $this->moduleFileUploads = [];
    }

    public function toggleModule(string $name): void
    {
        if (! Schema::hasTable('modules')) {
            Notification::make()->title(__('zerrocms.modules.table_missing'))->danger()->send();

            return;
        }
        if (! isset(app(ModuleManager::class)->discover()[$name])) {
            return;
        }
        try {
            $module = Module::firstOrNew(['name' => $name]);
            $module->enabled = ! ($module->exists && (bool) $module->enabled);
            $module->save();
            Notification::make()
                ->title($module->enabled ? __('zerrocms.modules.activated') : __('zerrocms.modules.deactivated'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.action_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }
}

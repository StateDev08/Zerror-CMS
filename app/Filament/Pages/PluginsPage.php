<?php

namespace App\Filament\Pages;

use App\Models\Plugin;
use App\Support\PackageInstaller;
use App\Support\PluginManager;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class PluginsPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Plugins';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Plugins verwalten';

    protected static ?int $navigationSort = 6;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.plugins';

    protected static function cmsPagePermission(): string
    {
        return 'manage_plugins';
    }

    public ?string $configuringPlugin = null;

    /** @var array<string, mixed> */
    public array $pluginConfigForm = [];

    /** @var array<int, array{key: string, type: string, label: string, default: mixed}> */
    public array $pluginConfigSchema = [];

    /** @var array<string, int> */
    public array $pluginOrders = [];

    public function mount(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }
        $plugins = app(PluginManager::class)->discover();
        $orders = Plugin::whereIn('name', array_keys($plugins))->pluck('order', 'name')->toArray();
        foreach (array_keys($plugins) as $name) {
            $this->pluginOrders[$name] = $orders[$name] ?? 999;
        }
    }

    public function getViewData(): array
    {
        $plugins = Schema::hasTable('plugins') ? app(PluginManager::class)->discover() : [];

        return [
            'plugins' => $plugins,
            'pluginsTableMissing' => ! Schema::hasTable('plugins'),
            'configuringPlugin' => $this->configuringPlugin,
            'pluginConfigSchema' => $this->pluginConfigSchema,
            'pluginConfigForm' => $this->pluginConfigForm,
            'installerUrl' => PackagesInstallerPage::getUrl(),
        ];
    }

    public function uninstallPlugin(string $name): void
    {
        $result = app(PackageInstaller::class)->uninstall(PackageInstaller::TYPE_PLUGIN, $name);
        unset($this->pluginOrders[$name]);
        if ($result['ok'] && $this->configuringPlugin === $name) {
            $this->closePluginConfig();
        }
        Notification::make()
            ->title($result['ok'] ? __('zerrocms.packages.delete_ok') : __('zerrocms.packages.delete_failed'))
            ->body($result['message'])
            ->{$result['ok'] ? 'success' : 'danger'}()
            ->send();
    }

    public function savePluginOrders(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }
        foreach ($this->pluginOrders as $name => $order) {
            Plugin::updateOrCreate(['name' => $name], ['order' => (int) $order]);
        }
        Notification::make()->title(__('zerrocms.plugins.order_saved'))->success()->send();
    }

    public function openPluginConfig(string $name): void
    {
        $schema = app(PluginManager::class)->getConfigSchema($name);
        $current = plugin_config($name);
        $form = [];
        foreach ($schema as $item) {
            $key = $item['key'] ?? '';
            if ($key !== '') {
                $form[$key] = $current[$key] ?? ($item['default'] ?? '');
            }
        }
        $this->configuringPlugin = $name;
        $this->pluginConfigSchema = $schema;
        $this->pluginConfigForm = $form;
    }

    public function savePluginConfig(): void
    {
        if ($this->configuringPlugin === null) {
            return;
        }
        try {
            set_plugin_config($this->configuringPlugin, $this->pluginConfigForm);
            $this->closePluginConfig();
            Notification::make()->title(__('zerrocms.plugins.saved'))->success()->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.save_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }

    public function closePluginConfig(): void
    {
        $this->configuringPlugin = null;
        $this->pluginConfigSchema = [];
        $this->pluginConfigForm = [];
    }

    public function togglePlugin(string $name): void
    {
        if (! Schema::hasTable('plugins') || ! isset(app(PluginManager::class)->discover()[$name])) {
            return;
        }
        try {
            $plugin = Plugin::firstOrNew(['name' => $name]);
            $plugin->enabled = ! ($plugin->exists && (bool) $plugin->enabled);
            if (! $plugin->exists) {
                $plugin->order = (int) Plugin::max('order') + 1;
            }
            $plugin->save();
            Notification::make()
                ->title($plugin->enabled ? __('zerrocms.plugins.activated') : __('zerrocms.plugins.deactivated'))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title(__('zerrocms.action_failed', ['message' => $e->getMessage()]))->danger()->send();
        }
    }
}

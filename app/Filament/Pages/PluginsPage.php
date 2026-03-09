<?php

namespace App\Filament\Pages;

use App\Models\Plugin;
use App\Support\PluginManager;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class PluginsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Plugins';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Plugins verwalten';
    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.plugins';

    public ?string $configuringPlugin = null;

    /** @var array<string, mixed> */
    public array $pluginConfigForm = [];

    /** @var array<int, array{key: string, type: string, label: string, default: mixed}> */
    public array $pluginConfigSchema = [];

    /** @var array<string, int> Reihenfolge pro Plugin-Name (für Reihenfolge speichern) */
    public array $pluginOrders = [];

    public function mount(): void
    {
        if (Schema::hasTable('plugins')) {
            $plugins = app(PluginManager::class)->discover();
            $orders = Plugin::whereIn('name', array_keys($plugins))->pluck('order', 'name')->toArray();
            foreach (array_keys($plugins) as $name) {
                $this->pluginOrders[$name] = $orders[$name] ?? 999;
            }
        }
    }

    public function getViewData(): array
    {
        $manager = app(PluginManager::class);
        $plugins = [];
        if (Schema::hasTable('plugins')) {
            $plugins = $manager->discover();
        }
        return [
            'plugins' => $plugins,
            'pluginsTableMissing' => ! Schema::hasTable('plugins'),
            'configuringPlugin' => $this->configuringPlugin,
            'pluginConfigSchema' => $this->pluginConfigSchema,
            'pluginConfigForm' => $this->pluginConfigForm,
        ];
    }

    public function savePluginOrders(): void
    {
        if (! Schema::hasTable('plugins')) {
            $this->notification()->danger(__('zerrocms.plugins.table_missing'));
            return;
        }
        try {
            foreach ($this->pluginOrders as $name => $order) {
                Plugin::updateOrCreate(
                    ['name' => $name],
                    ['order' => (int) $order]
                );
            }
            $this->notification()->success(__('zerrocms.plugins.order_saved'));
            $this->dispatch('refresh-page');
        } catch (\Throwable $e) {
            report($e);
            $this->notification()->danger(__('zerrocms.action_failed', ['message' => $e->getMessage()]));
        }
    }

    public function openPluginConfig(string $name): void
    {
        $manager = app(PluginManager::class);
        $schema = $manager->getConfigSchema($name);
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
            $this->configuringPlugin = null;
            $this->pluginConfigSchema = [];
            $this->pluginConfigForm = [];
            $this->notification()->success(__('zerrocms.plugins.saved'));
            $this->dispatch('refresh-page');
        } catch (\Throwable $e) {
            report($e);
            $this->notification()->danger('Speichern fehlgeschlagen: ' . $e->getMessage());
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
        if (! Schema::hasTable('plugins')) {
            $this->notification()->danger(__('zerrocms.plugins.table_missing'));
            return;
        }
        $discovered = app(PluginManager::class)->discover();
        if (! isset($discovered[$name])) {
            return;
        }
        try {
            $plugin = Plugin::firstOrNew(['name' => $name]);
            $plugin->enabled = ! $plugin->enabled;
            if (! $plugin->exists) {
                $plugin->order = Plugin::max('order') + 1;
            }
            $plugin->save();
            $this->notification()->success($plugin->enabled ? 'Plugin aktiviert.' : 'Plugin deaktiviert.');
            $this->dispatch('refresh-page');
        } catch (\Throwable $e) {
            report($e);
            $this->notification()->danger(__('zerrocms.action_failed', ['message' => $e->getMessage()]));
        }
    }
}

<?php

use App\Support\ServerStatusService;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroServerStatusWidget', false)) {
    class ZerroServerStatusWidget implements WidgetContract
    {
        public function id(): string { return 'server_status'; }
        public function title(): string { return __('widgets.server_status_title'); }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('server_status'), $config);
            $limit = max(1, min(20, (int) ($cfg['limit'] ?? 5)));
            $servers = array_slice(ServerStatusService::resolve($cfg), 0, $limit);
            $views = base_path('modules/server_status/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_server_status', $views);
            }

            return view('mod_server_status::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.server_status_title'),
                'servers' => $servers,
                'showPlayers' => filter_var($cfg['show_players'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'showMap' => filter_var($cfg['show_map'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'showMotd' => filter_var($cfg['show_motd'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'showPing' => filter_var($cfg['show_ping'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'showPageLink' => filter_var($cfg['show_page_link'] ?? true, FILTER_VALIDATE_BOOLEAN) && Route::has('servers.index'),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'limit' => ['type' => 'number', 'label' => __('widgets.limit'), 'default' => 5],
                'show_players' => ['type' => 'boolean', 'label' => __('widgets.server_status_show_players'), 'default' => true],
                'show_map' => ['type' => 'boolean', 'label' => __('widgets.server_status_show_map'), 'default' => true],
                'show_motd' => ['type' => 'boolean', 'label' => __('widgets.server_status_show_motd'), 'default' => false],
                'show_ping' => ['type' => 'boolean', 'label' => __('widgets.server_status_show_ping'), 'default' => true],
                'show_page_link' => ['type' => 'boolean', 'label' => __('widgets.show_all_link'), 'default' => true],
            ];
        }
    }
}

if (! class_exists('ZerroServerStatusModuleServiceProvider', false)) {
    class ZerroServerStatusModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}
        public function boot(): void
        {
            $views = base_path('modules/server_status/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_server_status');
            }
            app(WidgetRegistry::class)->register(new ZerroServerStatusWidget(), ['left', 'right']);
        }
    }
}

return ZerroServerStatusModuleServiceProvider::class;
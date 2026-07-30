<?php

use App\Models\Partner;
use App\Support\PluginManager;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroPartnersWidget', false)) {
    class ZerroPartnersWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'partners';
        }

        public function title(): string
        {
            return __('widgets.partner_slider_title');
        }

        public function render(array $config = []): string
        {
            $cfg = array_merge(plugin_config('partners'), $config);
            if (! filter_var($cfg['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                return '';
            }
            $partners = collect();
            if (Schema::hasTable('partners') && class_exists(Partner::class)) {
                $partners = Partner::query()->orderBy('order')->orderBy('name')->get();
            }
            $views = base_path('plugins/partners/views');
            if (is_dir($views)) {
                view()->addNamespace('plug_partners', $views);
            }

            return view('plug_partners::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.partner_slider_title'),
                'partners' => $partners,
                'speedMs' => max(1000, (int) ($cfg['speed_ms'] ?? 4000)),
                'visibleCount' => max(1, min(8, (int) ($cfg['visible_count'] ?? 4))),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'speed_ms' => ['type' => 'number', 'label' => __('widgets.partner_slider_speed'), 'default' => 4000],
                'visible_count' => ['type' => 'number', 'label' => __('widgets.partner_slider_visible'), 'default' => 4],
            ];
        }
    }
}

if (! class_exists('ZerroPartnersPluginServiceProvider', false)) {
    class ZerroPartnersPluginServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('plugins/partners/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'plug_partners');
            }

            $cfg = plugin_config('partners');
            if (filter_var($cfg['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                app(WidgetRegistry::class)->register(new ZerroPartnersWidget(), ['left', 'right', 'footer']);
            }

            if (filter_var($cfg['inject_footer'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                app(PluginManager::class)->registerBodyStartHtml(function () {
                    if (request()->is('admin', 'admin/*') || ! Schema::hasTable('partners')) {
                        return '';
                    }
                    $partners = Partner::query()->orderBy('order')->limit(8)->get();
                    if ($partners->isEmpty()) {
                        return '';
                    }
                    $html = '<div class="cms-partners-strip" style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:center;padding:0.5rem 1rem;opacity:0.9">';
                    foreach ($partners as $partner) {
                        $logo = trim((string) $partner->logo);
                        $logoUrl = $logo !== '' ? (str_starts_with($logo, 'http') ? $logo : storage_asset($logo)) : null;
                        $href = trim((string) $partner->url);
                        $inner = $logoUrl
                            ? '<img src="'.e($logoUrl).'" alt="'.e($partner->name).'" style="max-height:1.75rem;max-width:6rem;object-fit:contain">'
                            : e($partner->name);
                        if ($href !== '') {
                            $html .= '<a href="'.e($href).'" target="_blank" rel="noopener noreferrer">'.$inner.'</a>';
                        } else {
                            $html .= '<span>'.$inner.'</span>';
                        }
                    }
                    $html .= '</div>';

                    return $html;
                });
            }
        }
    }
}

return ZerroPartnersPluginServiceProvider::class;

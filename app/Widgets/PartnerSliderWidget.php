<?php

namespace App\Widgets;

use App\Models\Partner;
use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class PartnerSliderWidget implements WidgetContract
{
    public function id(): string
    {
        return 'partner_slider';
    }

    public function title(): string
    {
        return __('widgets.partner_slider_title', [], 'Partner');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(plugin_config('partner_slider'), $config);
        $speed = (int) ($config['speed'] ?? 3000);
        $visibleCount = (int) ($config['visible_count'] ?? 4);
        if ($visibleCount < 1) {
            $visibleCount = 4;
        }
        $partners = Partner::orderBy('order')->get();
        return View::make('components.widgets.partner-slider', [
            'partners' => $partners,
            'speed' => $speed,
            'visibleCount' => $visibleCount,
        ])->render();
    }

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array
    {
        return [];
    }
}

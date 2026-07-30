<?php

namespace App\Support;

use App\Models\WidgetInstance;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Schema;

class WidgetRenderer
{
    public function __construct(
        protected WidgetRegistry $registry
    ) {}

    public function slot(string $slot): string
    {
        if (! Schema::hasTable('widget_instances')) {
            return '';
        }

        $keys = match ($slot) {
            'right' => ['right', 'sidebar'],
            'left' => ['left', 'home'],
            default => [$slot],
        };

        $instances = WidgetInstance::query()
            ->whereIn('slot', $keys)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $html = '';
        foreach ($instances as $instance) {
            $widget = $this->registry->get($instance->widget_key);
            if (! $widget) {
                continue;
            }
            try {
                $rendered = $widget->render($instance->config ?? []);
                $html .= is_string($rendered) ? $rendered : '';
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $html;
    }

    public function hasContent(string $slot): bool
    {
        return trim(strip_tags($this->slot($slot))) !== '';
    }
}

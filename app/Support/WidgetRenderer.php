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
        $instances = WidgetInstance::where('slot', $slot)->orderBy('order')->get();
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
                // Widget render failed → log and continue so page still renders
            }
        }
        return $html;
    }
}

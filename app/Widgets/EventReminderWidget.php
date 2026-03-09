<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class EventReminderWidget implements WidgetContract
{
    public function id(): string
    {
        return 'event_reminder';
    }

    public function title(): string
    {
        return __('widgets.event_reminder_title', [], 'Nächste Events');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(plugin_config('event_reminder'), $config);
        $title = trim((string) ($config['title'] ?? ''));
        if ($title === '') {
            $title = __('widgets.event_reminder_title', [], 'Nächste Events');
        }
        $maxItems = (int) ($config['max_items'] ?? 5);
        $events = [];
        if (class_exists(\App\Models\Event::class)) {
            $events = \App\Models\Event::where('starts_at', '>=', now())
                ->where('visible', true)
                ->orderBy('starts_at')
                ->limit($maxItems)
                ->get();
        }
        return view('components.widgets.event-reminder', [
            'title' => $title,
            'events' => $events,
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

<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\Route;

class UpcomingEventsWidget implements WidgetContract
{
    public function id(): string
    {
        return 'upcoming_events';
    }

    public function title(): string
    {
        return __('widgets.upcoming_events');
    }

    public function render(array $config = []): string
    {
        $limit = max(1, min(20, (int) ($config['limit'] ?? 5)));
        $title = trim((string) ($config['title'] ?? '')) ?: $this->title();
        $showAll = filter_var($config['show_all_link'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $showLocation = filter_var($config['show_location'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $events = collect();
        if (class_exists(\App\Models\Event::class)) {
            $events = \App\Models\Event::query()
                ->where('starts_at', '>=', now())
                ->where('visible', true)
                ->orderBy('starts_at')
                ->limit($limit)
                ->get(['id', 'title', 'starts_at', 'ends_at', 'location', 'type']);
        }

        return view('components.widgets.upcoming-events', [
            'title' => $title,
            'events' => $events,
            'showAllLink' => $showAll && Route::has('calendar.index'),
            'showLocation' => $showLocation,
            'emptyText' => trim((string) ($config['empty_text'] ?? '')) ?: __('widgets.no_events'),
        ])->render();
    }

    public function configSchema(): array
    {
        return [
            'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
            'limit' => ['type' => 'number', 'label' => __('widgets.limit'), 'default' => 5],
            'show_all_link' => ['type' => 'boolean', 'label' => __('widgets.show_all_link'), 'default' => true],
            'show_location' => ['type' => 'boolean', 'label' => __('widgets.show_location'), 'default' => true],
            'empty_text' => ['type' => 'text', 'label' => __('widgets.empty_text'), 'default' => ''],
        ];
    }
}

<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

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
        $limit = (int) ($config['limit'] ?? 5);
        $events = [];
        if (class_exists(\App\Models\Event::class)) {
            $events = \App\Models\Event::where('starts_at', '>=', now())
                ->where('visible', true)
                ->orderBy('starts_at')
                ->limit($limit)
                ->get();
        }
        return view('components.widgets.upcoming-events', ['events' => $events])->render();
    }

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array
    {
        return [
            'limit' => [
                'type' => 'number',
                'label' => __('widgets.limit'),
                'default' => 5,
            ],
        ];
    }
}

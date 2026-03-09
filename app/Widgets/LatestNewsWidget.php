<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class LatestNewsWidget implements WidgetContract
{
    public function id(): string
    {
        return 'latest_news';
    }

    public function title(): string
    {
        return __('widgets.latest_news');
    }

    public function render(array $config = []): string
    {
        $limit = (int) ($config['limit'] ?? 5);
        $posts = [];
        if (class_exists(\App\Models\Post::class)) {
            $posts = \App\Models\Post::where('type', 'news')
                ->where('published', true)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        }
        return view('components.widgets.latest-news', ['posts' => $posts])->render();
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

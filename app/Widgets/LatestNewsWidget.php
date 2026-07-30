<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\Route;

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
        $limit = max(1, min(20, (int) ($config['limit'] ?? 5)));
        $title = trim((string) ($config['title'] ?? '')) ?: $this->title();
        $showAll = filter_var($config['show_all_link'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $posts = collect();
        if (class_exists(\App\Models\Post::class)) {
            $posts = \App\Models\Post::query()
                ->where('type', 'news')
                ->where('published', true)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(['id', 'title', 'slug', 'created_at', 'content']);
        }

        return view('components.widgets.latest-news', [
            'title' => $title,
            'posts' => $posts,
            'showAllLink' => $showAll && Route::has('news.index'),
            'emptyText' => trim((string) ($config['empty_text'] ?? '')) ?: __('widgets.no_news'),
        ])->render();
    }

    public function configSchema(): array
    {
        return [
            'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
            'limit' => ['type' => 'number', 'label' => __('widgets.limit'), 'default' => 5],
            'show_all_link' => ['type' => 'boolean', 'label' => __('widgets.show_all_link'), 'default' => true],
            'empty_text' => ['type' => 'text', 'label' => __('widgets.empty_text'), 'default' => ''],
        ];
    }
}

<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LatestForumPostsWidget implements WidgetContract
{
    public function id(): string
    {
        return 'latest_forum_posts';
    }

    public function title(): string
    {
        return __('widgets.latest_forum_posts');
    }

    public function render(array $config = []): string
    {
        $limit = max(1, min(20, (int) ($config['limit'] ?? 5)));
        $title = trim((string) ($config['title'] ?? '')) ?: $this->title();
        $showAll = filter_var($config['show_all_link'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $excerptLen = max(20, min(120, (int) ($config['excerpt_length'] ?? 48)));

        $posts = collect();
        if (class_exists(\App\Models\ForumPost::class)) {
            $posts = \App\Models\ForumPost::with(['thread.forum', 'user'])
                ->orderByDesc('created_at')
                ->limit($limit * 3)
                ->get()
                ->filter(function ($post) {
                    try {
                        $forum = $post->thread?->forum;
                        if (! $forum) {
                            return false;
                        }
                        $readRankId = $forum->read_rank_id;
                        if (! $readRankId) {
                            return true;
                        }
                        if (! auth()->check()) {
                            return false;
                        }
                        $userRank = auth()->user()->clanMember?->rank;
                        $forumRank = $forum->readRank;

                        return $forumRank && $userRank && $userRank->order <= $forumRank->order;
                    } catch (\Throwable) {
                        return false;
                    }
                })
                ->take($limit)
                ->values();
        }

        return view('components.widgets.latest-forum-posts', [
            'title' => $title,
            'posts' => $posts,
            'showAllLink' => $showAll && Route::has('forum.index'),
            'excerptLength' => $excerptLen,
            'emptyText' => trim((string) ($config['empty_text'] ?? '')) ?: __('widgets.no_forum_posts'),
        ])->render();
    }

    public function configSchema(): array
    {
        return [
            'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
            'limit' => ['type' => 'number', 'label' => __('widgets.limit'), 'default' => 5],
            'show_all_link' => ['type' => 'boolean', 'label' => __('widgets.show_all_link'), 'default' => true],
            'excerpt_length' => ['type' => 'number', 'label' => __('widgets.excerpt_length'), 'default' => 48],
            'empty_text' => ['type' => 'text', 'label' => __('widgets.empty_text'), 'default' => ''],
        ];
    }
}

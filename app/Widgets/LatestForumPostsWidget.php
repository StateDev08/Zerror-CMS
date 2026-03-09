<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

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
        $limit = (int) ($config['limit'] ?? 5);
        $posts = [];
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
                        $userRankId = auth()->user()->clanMember?->rank_id;
                        if (! $userRankId) {
                            return false;
                        }
                        $forumRank = $forum->readRank;
                        $userRank = auth()->user()->clanMember?->rank;
                        return $forumRank && $userRank && $userRank->order <= $forumRank->order;
                    } catch (\Throwable $e) {
                        return false;
                    }
                })
                ->take($limit)
                ->values();
        }
        return view('components.widgets.latest-forum-posts', ['posts' => $posts])->render();
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

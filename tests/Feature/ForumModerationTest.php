<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumForum;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function createForum(): ForumForum
    {
        $category = ForumCategory::create([
            'name' => 'General',
            'slug' => 'general',
            'order' => 0,
        ]);

        return ForumForum::create([
            'category_id' => $category->id,
            'name' => 'Chat',
            'slug' => 'chat',
            'order' => 0,
        ]);
    }

    public function test_author_can_edit_own_post(): void
    {
        $user = User::factory()->create();
        $forum = $this->createForum();
        $thread = ForumThread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Hallo',
        ]);
        $post = ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Erster Text',
        ]);

        $this->actingAs($user)
            ->put(route('forum.post.update', $post), [
                'body' => 'Geänderter Text',
            ])
            ->assertRedirect();

        $this->assertSame('Geänderter Text', $post->fresh()->body);
    }

    public function test_guest_cannot_edit_post(): void
    {
        $user = User::factory()->create();
        $forum = $this->createForum();
        $thread = ForumThread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Hallo',
        ]);
        $post = ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Erster Text',
        ]);

        $this->get(route('forum.post.edit', $post))
            ->assertRedirect(route('login'));
    }
}

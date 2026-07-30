<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumForum;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Support\HtmlContent;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::with(['forums' => fn ($q) => $q->withCount('threads')->orderBy('order')])
            ->orderBy('order')
            ->get();
        return view('theme::forum.index', ['categories' => $categories]);
    }

    public function showForum(ForumForum $forum)
    {
        $this->authorizeRead($forum);
        $forum->loadMissing('writeRank');
        $threads = $forum->threads()->withCount('posts')->with(['user.clanMember.rank', 'user.roles'])->orderByDesc('pinned')->orderByDesc('updated_at')->paginate(15);
        return view('theme::forum.forum', ['forum' => $forum, 'threads' => $threads]);
    }

    public function showThread(ForumThread $thread)
    {
        $this->authorizeRead($thread->forum);
        $posts = $thread->posts()->with(['user.clanMember.rank', 'user.roles'])->orderBy('created_at')->paginate(15);
        return view('theme::forum.thread', ['thread' => $thread->load('forum'), 'posts' => $posts]);
    }

    public function createThread(ForumForum $forum)
    {
        $this->authorizeWrite($forum);
        return view('theme::forum.create-thread', ['forum' => $forum]);
    }

    public function storeThread(Request $request, ForumForum $forum)
    {
        $this->authorizeWrite($forum);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:50000',
        ]);
        $body = $this->sanitizeForumBody($validated['body']);
        $thread = $forum->threads()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
        ]);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body' => $body,
        ]);
        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.post_created'));
    }

    public function reply(Request $request, ForumThread $thread)
    {
        $this->authorizeWrite($thread->forum);
        if ($thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_reply'));
        }
        $validated = $request->validate(['body' => 'required|string|max:50000']);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body' => $this->sanitizeForumBody($validated['body']),
        ]);
        $thread->touch();
        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.post_created'));
    }

    public function editPost(ForumPost $post)
    {
        $this->authorizeManagePost($post);
        $post->load('thread.forum');
        if ($post->thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        return view('theme::forum.edit-post', ['post' => $post]);
    }

    public function updatePost(Request $request, ForumPost $post)
    {
        $this->authorizeManagePost($post);
        $post->load('thread.forum');
        if ($post->thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        $validated = $request->validate(['body' => 'required|string|max:50000']);
        $post->update(['body' => $this->sanitizeForumBody($validated['body'])]);

        return redirect()->route('forum.thread.show', $post->thread)->with('success', __('forum.updated'));
    }

    public function destroyPost(ForumPost $post)
    {
        $this->authorizeManagePost($post);
        $post->load('thread.forum');
        if ($post->thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        $thread = $post->thread;
        $post->delete();

        if ($thread->posts()->count() === 0) {
            $forum = $thread->forum;
            $thread->delete();
            return redirect()->route('forum.show', $forum)->with('success', __('forum.deleted'));
        }

        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.deleted'));
    }

    public function editThread(ForumThread $thread)
    {
        $this->authorizeManageThread($thread);
        if ($thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        return view('theme::forum.edit-thread', ['thread' => $thread->load('forum')]);
    }

    public function updateThread(Request $request, ForumThread $thread)
    {
        $this->authorizeManageThread($thread);
        if ($thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        $validated = $request->validate(['title' => 'required|string|max:255']);
        $thread->update(['title' => $validated['title']]);

        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.updated'));
    }

    public function destroyThread(ForumThread $thread)
    {
        $this->authorizeManageThread($thread);
        if ($thread->locked && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_locked'));
        }

        $forum = $thread->forum;
        $thread->delete();

        return redirect()->route('forum.show', $forum)->with('success', __('forum.deleted'));
    }

    public function togglePin(ForumThread $thread)
    {
        abort_unless($this->isAdmin(), 403);
        $thread->update(['pinned' => ! $thread->pinned]);

        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.updated'));
    }

    public function toggleLock(ForumThread $thread)
    {
        abort_unless($this->isAdmin(), 403);
        $thread->update(['locked' => ! $thread->locked]);

        return redirect()->route('forum.thread.show', $thread)->with('success', __('forum.updated'));
    }

    protected function isAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->can('access_admin') || $user->hasRole('super-admin');
    }

    protected function sanitizeForumBody(string $body): string
    {
        return HtmlContent::sanitizeRequired($body, 'body', __('forum.first_post'));
    }

    protected function authorizeManagePost(ForumPost $post): void
    {
        $this->authorizeRead($post->thread->forum);
        if (! auth()->check()) {
            abort(403, __('forum.forbidden_guest'));
        }
        if (auth()->id() !== $post->user_id && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_manage'));
        }
    }

    protected function authorizeManageThread(ForumThread $thread): void
    {
        $this->authorizeRead($thread->forum);
        if (! auth()->check()) {
            abort(403, __('forum.forbidden_guest'));
        }
        if (auth()->id() !== $thread->user_id && ! $this->isAdmin()) {
            abort(403, __('forum.forbidden_manage'));
        }
    }

    protected function authorizeRead(ForumForum $forum): void
    {
        if ($this->isAdmin()) {
            return;
        }
        if ($forum->read_rank_id && ! auth()->check()) {
            abort(403, __('forum.forbidden_guest'));
        }
        if ($forum->read_rank_id && auth()->check()) {
            $userRankId = auth()->user()->clanMember?->rank_id;
            if (! $userRankId) {
                abort(403, __('forum.forbidden_rank'));
            }
            $forumRank = $forum->readRank;
            $userRank = auth()->user()->clanMember?->rank;
            if ($forumRank && $userRank && $userRank->order > $forumRank->order) {
                abort(403, __('forum.forbidden_rank'));
            }
        }
    }

    protected function authorizeWrite(ForumForum $forum): void
    {
        if (! auth()->check()) {
            abort(403, __('forum.forbidden_guest'));
        }
        if ($this->isAdmin()) {
            return;
        }
        if ($forum->write_rank_id) {
            $userRankId = auth()->user()->clanMember?->rank_id;
            if (! $userRankId) {
                abort(403, __('forum.forbidden_reply'));
            }
            $forumRank = $forum->writeRank;
            $userRank = auth()->user()->clanMember?->rank;
            if ($forumRank && $userRank && $userRank->order > $forumRank->order) {
                abort(403, __('forum.forbidden_reply'));
            }
        }
    }
}

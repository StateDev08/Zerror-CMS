<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumForum;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::with('forums')->orderBy('order')->get();
        return view('theme::forum.index', ['categories' => $categories]);
    }

    public function showForum(ForumForum $forum)
    {
        $this->authorizeRead($forum);
        $threads = $forum->threads()->withCount('posts')->with('user')->orderByDesc('pinned')->orderByDesc('updated_at')->paginate(15);
        return view('theme::forum.forum', ['forum' => $forum, 'threads' => $threads]);
    }

    public function showThread(ForumThread $thread)
    {
        $this->authorizeRead($thread->forum);
        $posts = $thread->posts()->with('user')->orderBy('created_at')->paginate(15);
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
            'body' => 'required|string|max:10000',
        ]);
        $thread = $forum->threads()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
        ]);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);
        return redirect()->route('forum.thread.show', $thread)->with('success', true);
    }

    public function reply(Request $request, ForumThread $thread)
    {
        $this->authorizeWrite($thread->forum);
        if ($thread->locked) {
            abort(403, __('forum.forbidden_reply'));
        }
        $validated = $request->validate(['body' => 'required|string|max:10000']);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);
        $thread->touch();
        return redirect()->route('forum.thread.show', $thread)->with('success', true);
    }

    protected function authorizeRead(ForumForum $forum): void
    {
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

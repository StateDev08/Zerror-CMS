<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::with('options')->where('active', true)->orderByDesc('created_at')->get();
        return view('theme::polls.index', ['polls' => $polls]);
    }

    public function show(Poll $poll)
    {
        if (! $poll->active) {
            abort(404);
        }
        $poll->load('options');
        $hasVoted = $this->hasVoted($poll);
        return view('theme::polls.show', ['poll' => $poll, 'hasVoted' => $hasVoted]);
    }

    public function vote(Request $request, Poll $poll)
    {
        if (! $poll->active) {
            return redirect()->route('polls.show', $poll)->with('error', __('polls.closed'));
        }
        $request->validate(['option_id' => 'required|exists:poll_options,id']);
        $optionId = (int) $request->option_id;
        $option = PollOption::where('poll_id', $poll->id)->findOrFail($optionId);

        if ($this->hasVoted($poll)) {
            return redirect()->route('polls.show', $poll)->with('error', __('polls.already_voted'));
        }

        PollVote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
        ]);
        $option->increment('votes_count');

        return redirect()->route('polls.show', $poll)->with('voted', true);
    }

    private function hasVoted(Poll $poll): bool
    {
        $q = PollVote::where('poll_id', $poll->id);
        if (auth()->check()) {
            $q->where('user_id', auth()->id());
        } else {
            $q->where('ip_address', request()->ip());
        }
        return $q->exists();
    }
}

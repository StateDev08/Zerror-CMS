<?php

namespace App\Http\Controllers;

use App\Models\ClanFeedback;
use Illuminate\Http\Request;

class ClanFeedbackController extends Controller
{
    public function index()
    {
        return view('theme::clan-feedback.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email',
            'message' => 'required|string|max:5000',
        ]);
        ClanFeedback::create($validated);
        return redirect()->route('clan-feedback.index')->with('feedback_sent', true);
    }
}

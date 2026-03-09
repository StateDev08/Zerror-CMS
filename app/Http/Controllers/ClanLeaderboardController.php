<?php

namespace App\Http\Controllers;

use App\Models\ClanLeaderboardCategory;

class ClanLeaderboardController extends Controller
{
    public function index()
    {
        $categories = ClanLeaderboardCategory::with(['entries' => fn ($q) => $q->orderByDesc('score')])
            ->orderBy('order')
            ->get();
        return view('theme::clan-leaderboard.index', ['categories' => $categories]);
    }

    public function category(ClanLeaderboardCategory $category)
    {
        $category->load(['entries' => fn ($q) => $q->orderByDesc('score')]);
        return view('theme::clan-leaderboard.category', ['category' => $category]);
    }
}

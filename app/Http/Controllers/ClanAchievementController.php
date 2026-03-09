<?php

namespace App\Http\Controllers;

use App\Models\ClanAchievement;

class ClanAchievementController extends Controller
{
    public function index()
    {
        $achievements = ClanAchievement::where('visible', true)->orderBy('order')->get();
        return view('theme::clan-achievements.index', ['achievements' => $achievements]);
    }
}

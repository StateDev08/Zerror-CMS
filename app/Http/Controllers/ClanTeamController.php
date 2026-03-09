<?php

namespace App\Http\Controllers;

use App\Models\ClanTeam;

class ClanTeamController extends Controller
{
    public function index()
    {
        $teams = ClanTeam::with('members')->where('visible', true)->orderBy('order')->get();
        return view('theme::clan-teams.index', ['teams' => $teams]);
    }

    public function show(ClanTeam $clanTeam)
    {
        if (! $clanTeam->visible) {
            abort(404);
        }
        $clanTeam->load('members');
        return view('theme::clan-teams.show', ['team' => $clanTeam]);
    }
}

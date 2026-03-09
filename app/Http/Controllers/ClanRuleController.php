<?php

namespace App\Http\Controllers;

use App\Models\ClanRule;

class ClanRuleController extends Controller
{
    public function index()
    {
        $rules = ClanRule::where('visible', true)->orderBy('order')->get();
        return view('theme::clan-rules.index', ['rules' => $rules]);
    }
}

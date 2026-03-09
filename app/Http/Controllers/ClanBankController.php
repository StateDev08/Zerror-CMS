<?php

namespace App\Http\Controllers;

use App\Models\ClanBankCategory;

class ClanBankController extends Controller
{
    public function index()
    {
        $categories = ClanBankCategory::with(['items' => fn ($q) => $q->where('visible', true)])
            ->orderBy('order')
            ->get();
        return view('theme::clan-bank.index', ['categories' => $categories]);
    }
}

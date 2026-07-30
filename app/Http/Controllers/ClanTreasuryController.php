<?php

namespace App\Http\Controllers;

use App\Models\ClanTreasuryCategory;
use App\Models\ClanTreasuryEntry;

class ClanTreasuryController extends Controller
{
    public function index()
    {
        $categories = ClanTreasuryCategory::with(['entries' => fn ($q) => $q->orderByDesc('entry_date')])
            ->orderBy('order')
            ->get();

        $totalIncome = (float) ClanTreasuryEntry::where('type', 'income')->sum('amount');
        $totalExpense = (float) ClanTreasuryEntry::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('theme::clan-treasury.index', [
            'categories' => $categories,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
        ]);
    }
}

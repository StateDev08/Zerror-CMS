<?php

namespace App\Http\Controllers;

use App\Models\ClanDocument;
use App\Models\ClanDocumentCategory;

class ClanDocumentController extends Controller
{
    public function index()
    {
        $categories = ClanDocumentCategory::with(['documents' => fn ($q) => $q->where('visible', true)->orderBy('order')])
            ->orderBy('order')
            ->get();
        return view('theme::clan-documents.index', ['categories' => $categories]);
    }

    public function show(ClanDocument $document)
    {
        if (! $document->visible) {
            abort(404);
        }
        return view('theme::clan-documents.show', ['document' => $document]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ClanAnnouncement;

class ClanAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = ClanAnnouncement::where('visible', true)
            ->where(function ($q) {
            $q->whereNull('visible_until')->orWhere('visible_until', '>=', now());
        })
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('theme::clan-announcements.index', ['announcements' => $announcements]);
    }
}

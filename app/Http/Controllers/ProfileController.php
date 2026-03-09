<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Öffentliche Profilseite eines Users anzeigen.
     */
    public function show(Request $request, User $user): View
    {
        return view('theme::profile.show', ['profileUser' => $user]);
    }
}

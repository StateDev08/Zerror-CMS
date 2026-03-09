<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);
        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();
        if ($existing) {
            return redirect()->back()->with('newsletter_status', 'already');
        }
        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'token' => Str::random(32),
        ]);
        return redirect()->back()->with('newsletter_status', 'subscribed');
    }
}

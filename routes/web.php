<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\StorageServeController;
use App\Models\ClanMember;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

// Storage-Dateien über Laravel ausliefern (funktioniert ohne Symlink, umgeht 403/404)
Route::get('app-storage/{path}', StorageServeController::class)->where('path', '.*')->name('storage.serve');

// Status-Check für Deployment/Monitoring (DB + installed)
Route::get('status', function () {
    $dbOk = false;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbOk = true;
    } catch (\Throwable $e) {
        // ignore
    }
    $installed = \App\Support\Installer::isInstalled();
    $status = $dbOk && $installed ? 200 : 503;
    return response()->json([
        'ok' => $dbOk && $installed,
        'database' => $dbOk,
        'installed' => $installed,
    ], $status);
})->name('status');

// Installer (nur wenn noch nicht installiert)
Route::middleware('install.redirect')->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::post('/database', [InstallController::class, 'database'])->name('database');
    Route::get('/migrate', [InstallController::class, 'migrate'])->name('migrate');
    Route::post('/finish', [InstallController::class, 'finish'])->name('finish');
});

// Frontend Login / Register / Logout
Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::get('register', [App\Http\Controllers\Auth\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
});
Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', function () {
    return view('theme::home');
})->name('home');

Route::get('/crafting', [App\Http\Controllers\CraftingController::class, 'index'])->name('crafting.index');
Route::get('/crafting/erstellen', [App\Http\Controllers\CraftingController::class, 'create'])->middleware('auth')->name('crafting.create');
Route::post('/crafting', [App\Http\Controllers\CraftingController::class, 'store'])->middleware('auth')->name('crafting.store');

Route::get('/news', function () {
    $posts = Post::where('type', 'news')->where('published', true)->orderByDesc('created_at')->paginate(10);
    return view('theme::news.index', ['posts' => $posts]);
})->name('news.index');

Route::get('/news/{slug}', function (string $slug) {
    $post = Post::where('type', 'news')->where('published', true)->where('slug', $slug)->firstOrFail();
    return view('theme::news.show', ['post' => $post]);
})->name('news.show');

Route::get('/roster', function () {
    $members = ClanMember::with('rank')->where('visible', true)->orderBy('order')->get()->groupBy('rank_id');
    $ranks = \App\Models\Rank::orderBy('order')->get();
    return view('theme::roster.index', ['members' => $members, 'ranks' => $ranks]);
})->name('roster.index');

Route::get('/calendar', function () {
    $events = Event::where('visible', true)->where('starts_at', '>=', now()->startOfMonth())->orderBy('starts_at')->paginate(20);
    return view('theme::calendar.index', ['events' => $events]);
})->name('calendar.index');

Route::get('/calendar/event/{id}', function (int $id) {
    $event = Event::where('visible', true)->findOrFail($id);
    return view('theme::calendar.show', ['event' => $event]);
})->name('calendar.show');

Route::get('/apply', function () {
    if (! (bool) filter_var(setting('applications_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
        return view('apply-disabled');
    }
    return view('theme::apply.index');
})->name('apply.index');

Route::post('/apply', function (\Illuminate\Http\Request $request) {
    if (! (bool) filter_var(setting('applications_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
        abort(403);
    }
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string|max:5000',
    ]);
    $application = \App\Models\Application::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'message' => $validated['message'],
    ]);
    $notifyEmail = config('clan.application_notify_email');
    if ($notifyEmail) {
        \Illuminate\Support\Facades\Mail::to($notifyEmail)->send(new \App\Mail\ApplicationReceivedMail($application));
    }
    $webhookUrl = config('clan.discord_webhook_url');
    if ($webhookUrl) {
        try {
            \Illuminate\Support\Facades\Http::post($webhookUrl, [
                'content' => __('mail.application_received_subject', ['name' => config('clan.name')]) . "\n" . $application->name . ' (' . $application->email . ')',
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
    return redirect()->route('apply.index')->with('application_sent', true);
})->middleware('throttle:5,1')->name('apply.store');

Route::get('/page/{slug}', function (string $slug) {
    $page = Post::where('type', 'page')->where('slug', $slug)->firstOrFail();
    return view('theme::page.show', ['slug' => $slug, 'page' => $page]);
})->name('page.show');

Route::get('/gallery', [App\Http\Controllers\GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/album/{album}', [App\Http\Controllers\GalleryController::class, 'showAlbum'])->name('gallery.album');

Route::get('/downloads', [App\Http\Controllers\DownloadController::class, 'index'])->name('downloads.index');
Route::get('/downloads/file/{download}', [App\Http\Controllers\DownloadController::class, 'file'])->name('downloads.file');

Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'index'])->name('partners.index');

Route::get('/polls', [App\Http\Controllers\PollController::class, 'index'])->name('polls.index');
Route::get('/polls/{poll}', [App\Http\Controllers\PollController::class, 'show'])->name('polls.show');
Route::post('/polls/{poll}/vote', [App\Http\Controllers\PollController::class, 'vote'])->name('polls.vote');

Route::get('/newsletter', function () { return view('theme::newsletter.subscribe'); })->name('newsletter.index');
Route::post('/newsletter', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::get('/wiki', [App\Http\Controllers\WikiController::class, 'index'])->name('wiki.index');
Route::get('/wiki/search', [App\Http\Controllers\WikiController::class, 'search'])->name('wiki.search');
Route::get('/wiki/category/{category:slug}', [App\Http\Controllers\WikiController::class, 'category'])->name('wiki.category')->scopeBindings();
Route::get('/wiki/{slug}', [App\Http\Controllers\WikiController::class, 'show'])->name('wiki.show');

Route::get('/marketplace', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/category/{category:slug}', [App\Http\Controllers\MarketplaceController::class, 'category'])->name('marketplace.category')->scopeBindings();
Route::get('/marketplace/{listing:slug}', [App\Http\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');

Route::get('/jobs', [App\Http\Controllers\JobOfferController::class, 'index'])->name('jobs.index');
Route::get('/jobs/category/{category:slug}', [App\Http\Controllers\JobOfferController::class, 'category'])->name('jobs.category')->scopeBindings();
Route::get('/jobs/{jobOffer:slug}', [App\Http\Controllers\JobOfferController::class, 'show'])->name('jobs.show');

// User Control Panel (eingeloggt)
Route::middleware('auth')->prefix('usercp')->name('usercp.')->group(function () {
    Route::get('/', [App\Http\Controllers\UserCpController::class, 'index'])->name('index');
    Route::get('/profile', [App\Http\Controllers\UserCpController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\UserCpController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password', [App\Http\Controllers\UserCpController::class, 'password'])->name('password');
    Route::put('/password', [App\Http\Controllers\UserCpController::class, 'updatePassword'])->name('password.update');
    Route::get('/auftraege', [App\Http\Controllers\UserCpController::class, 'itemRequests'])->name('item-requests');
});

Route::get('/user/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.public')->scopeBindings();

Route::get('/forum', [App\Http\Controllers\ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{forum}', [App\Http\Controllers\ForumController::class, 'showForum'])->name('forum.show');
Route::get('/forum/{forum}/thread/create', [App\Http\Controllers\ForumController::class, 'createThread'])->name('forum.thread.create');
Route::post('/forum/{forum}/thread', [App\Http\Controllers\ForumController::class, 'storeThread'])->name('forum.thread.store');
Route::get('/forum/thread/{thread}', [App\Http\Controllers\ForumController::class, 'showThread'])->name('forum.thread.show');
Route::post('/forum/thread/{thread}/reply', [App\Http\Controllers\ForumController::class, 'reply'])->name('forum.thread.reply');

// Clan-Module
Route::get('/clan/teams', [App\Http\Controllers\ClanTeamController::class, 'index'])->name('clan-teams.index');
Route::get('/clan/teams/{clanTeam:slug}', [App\Http\Controllers\ClanTeamController::class, 'show'])->name('clan-teams.show')->scopeBindings();
Route::get('/clan/bank', [App\Http\Controllers\ClanBankController::class, 'index'])->name('clan-bank.index');
Route::get('/clan/rules', [App\Http\Controllers\ClanRuleController::class, 'index'])->name('clan-rules.index');
Route::get('/clan/leaderboard', [App\Http\Controllers\ClanLeaderboardController::class, 'index'])->name('clan-leaderboard.index');
Route::get('/clan/leaderboard/{category:slug}', [App\Http\Controllers\ClanLeaderboardController::class, 'category'])->name('clan-leaderboard.category')->scopeBindings();
Route::get('/clan/documents', [App\Http\Controllers\ClanDocumentController::class, 'index'])->name('clan-documents.index');
Route::get('/clan/documents/{document}', [App\Http\Controllers\ClanDocumentController::class, 'show'])->name('clan-documents.show');
Route::get('/clan/feedback', [App\Http\Controllers\ClanFeedbackController::class, 'index'])->name('clan-feedback.index');
Route::post('/clan/feedback', [App\Http\Controllers\ClanFeedbackController::class, 'store'])->middleware('throttle:5,1')->name('clan-feedback.store');
Route::get('/clan/announcements', [App\Http\Controllers\ClanAnnouncementController::class, 'index'])->name('clan-announcements.index');
Route::get('/clan/achievements', [App\Http\Controllers\ClanAchievementController::class, 'index'])->name('clan-achievements.index');

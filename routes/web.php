<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\StorageServeController;
use App\Models\ClanMember;
use App\Models\Event;
use App\Models\Post;
use App\Models\User;
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
    Route::post('/dependencies', [InstallController::class, 'dependencies'])->name('dependencies');
    Route::post('/database', [InstallController::class, 'database'])->name('database');
    Route::get('/migrate', [InstallController::class, 'migrate'])->name('migrate');
    Route::post('/site', [InstallController::class, 'site'])->name('site');
    Route::post('/discord', [InstallController::class, 'discord'])->name('discord');
    Route::post('/mail-test', [InstallController::class, 'mailTest'])->name('mail-test');
    Route::post('/mail', [InstallController::class, 'mail'])->name('mail');
    Route::post('/finish', [InstallController::class, 'finish'])->name('finish');
});

// Frontend Login / Register / Logout / Password Reset
Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::get('register', [App\Http\Controllers\Auth\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::get('forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
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
    $post = Post::where('type', 'news')->where('published', true)->where('slug', $slug)->with('author')->firstOrFail();
    return view('theme::news.show', ['post' => $post]);
})->name('news.show');

Route::get('/roster', function () {
    $users = User::query()
        ->with(['clanMember.rank', 'roles'])
        ->orderBy('name')
        ->get();

    $linkedUserIds = $users->pluck('id')->all();

    $orphanMembers = ClanMember::with('rank')
        ->where('visible', true)
        ->where(function ($q) use ($linkedUserIds) {
            $q->whereNull('user_id');
            if ($linkedUserIds !== []) {
                $q->orWhereNotIn('user_id', $linkedUserIds);
            }
        })
        ->orderBy('order')
        ->get();

    $entries = $users->map(function (User $user) {
        $clan = $user->clanMember;
        $visibleClan = $clan && $clan->visible ? $clan : null;
        $rankName = $visibleClan?->rank?->name
            ?? $user->getRoleNames()
                ->map(fn ($n) => ucwords(str_replace('-', ' ', (string) $n)))
                ->first();

        return [
            'name' => $visibleClan?->display_name ?: $user->name,
            'avatar' => ($visibleClan?->avatar ? storage_asset($visibleClan->avatar) : null) ?: $user->avatar_url,
            'position' => $visibleClan?->position,
            'rank' => $rankName,
            'rank_color' => $visibleClan?->rank?->color,
            'registered_at' => $user->created_at,
        ];
    });

    foreach ($orphanMembers as $member) {
        $entries->push([
            'name' => $member->display_name,
            'avatar' => $member->avatar ? storage_asset($member->avatar) : null,
            'position' => $member->position,
            'rank' => $member->rank?->name,
            'rank_color' => $member->rank?->color,
            'registered_at' => $member->created_at,
        ]);
    }

    return view('theme::roster.index', [
        'entries' => $entries->values(),
    ]);
})->name('roster.index');


Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

Route::get('/calendar/event/{id}', [App\Http\Controllers\CalendarController::class, 'show'])->name('calendar.show');

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
        'message' => 'required|string|max:50000',
    ]);

    $html = \App\Support\HtmlContent::sanitizeRequired(
        $validated['message'],
        'message',
        __('apply.message')
    );

    $application = \App\Models\Application::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'message' => $html,
    ]);
    $notifyEmail = \App\Support\SiteContent::applicationNotifyEmail();
    if ($notifyEmail) {
        \Illuminate\Support\Facades\Mail::to($notifyEmail)->send(new \App\Mail\ApplicationReceivedMail($application));
    }
    $webhookUrl = \App\Support\SiteContent::discordWebhookUrl();
    if ($webhookUrl) {
        try {
            \Illuminate\Support\Facades\Http::post($webhookUrl, [
                'content' => __('mail.application_received_subject', ['name' => site_name()]) . "\n" . $application->name . ' (' . $application->email . ')',
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
    return redirect()->route('apply.index')->with('application_sent', true);
})->middleware('throttle:5,1')->name('apply.store');

Route::get('/page/{slug}', function (string $slug) {
    $cmsPage = \App\Models\CmsPage::query()
        ->where('slug', $slug)
        ->where('published', true)
        ->first();

    if ($cmsPage) {
        return view('theme::page.show', ['slug' => $slug, 'page' => $cmsPage]);
    }

    $page = Post::where('type', 'page')
        ->where('slug', $slug)
        ->where('published', true)
        ->firstOrFail();

    return view('theme::page.show', ['slug' => $slug, 'page' => $page]);
})->name('page.show');

// Kurz-URLs für Rechtsseiten
foreach (['impressum', 'datenschutz', 'agb', 'cookies'] as $legalSlug) {
    Route::redirect('/'.$legalSlug, '/page/'.$legalSlug, 301);
}

Route::get('/servers', [App\Http\Controllers\ServerStatusController::class, 'index'])->name('servers.index');

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
Route::get('/newsletter/unsubscribe/{token}', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/wiki', [App\Http\Controllers\WikiController::class, 'index'])->name('wiki.index');
Route::get('/wiki/search', [App\Http\Controllers\WikiController::class, 'search'])->name('wiki.search');
Route::get('/wiki/category/{category:slug}', [App\Http\Controllers\WikiController::class, 'category'])->name('wiki.category')->scopeBindings();
Route::get('/wiki/{slug}', [App\Http\Controllers\WikiController::class, 'show'])->name('wiki.show');

Route::get('/marketplace', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/category/{category:slug}', [App\Http\Controllers\MarketplaceController::class, 'category'])->name('marketplace.category')->scopeBindings();
Route::middleware('auth')->group(function () {
    Route::get('/marketplace/create', [App\Http\Controllers\MarketplaceController::class, 'create'])->name('marketplace.create');
    Route::post('/marketplace', [App\Http\Controllers\MarketplaceController::class, 'store'])->name('marketplace.store');
    Route::get('/marketplace/{listing:slug}/edit', [App\Http\Controllers\MarketplaceController::class, 'edit'])->name('marketplace.edit')->scopeBindings();
    Route::put('/marketplace/{listing:slug}', [App\Http\Controllers\MarketplaceController::class, 'update'])->name('marketplace.update')->scopeBindings();
    Route::delete('/marketplace/{listing:slug}', [App\Http\Controllers\MarketplaceController::class, 'destroy'])->name('marketplace.destroy')->scopeBindings();
});
Route::get('/marketplace/{listing:slug}', [App\Http\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');

Route::get('/jobs', [App\Http\Controllers\JobOfferController::class, 'index'])->name('jobs.index');
Route::get('/jobs/category/{category:slug}', [App\Http\Controllers\JobOfferController::class, 'category'])->name('jobs.category')->scopeBindings();
Route::get('/jobs/{jobOffer:slug}', [App\Http\Controllers\JobOfferController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{jobOffer:slug}/apply', [App\Http\Controllers\JobOfferController::class, 'apply'])->middleware('throttle:5,1')->name('jobs.apply')->scopeBindings();

// User Control Panel (eingeloggt)
Route::middleware('auth')->prefix('usercp')->name('usercp.')->group(function () {
    Route::get('/', [App\Http\Controllers\UserCpController::class, 'index'])->name('index');
    Route::get('/profile', [App\Http\Controllers\UserCpController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\UserCpController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password', [App\Http\Controllers\UserCpController::class, 'password'])->name('password');
    Route::put('/password', [App\Http\Controllers\UserCpController::class, 'updatePassword'])->name('password.update');
    Route::get('/discord', [App\Http\Controllers\UserCpController::class, 'discord'])->name('discord');
    Route::post('/discord/generate', [App\Http\Controllers\UserCpController::class, 'generateDiscordLinkToken'])->name('discord.generate');
    Route::post('/discord/unlink', [App\Http\Controllers\UserCpController::class, 'unlinkDiscord'])->name('discord.unlink');
    Route::get('/notifications', [App\Http\Controllers\UserCpController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [App\Http\Controllers\UserCpController::class, 'markAllRead'])->name('notifications.read-all');
    Route::match(['get', 'post'], '/notifications/{notification}/read', [App\Http\Controllers\UserCpController::class, 'markRead'])->name('notifications.read');
    Route::get('/auftraege', [App\Http\Controllers\UserCpController::class, 'itemRequests'])->name('item-requests');
});

Route::get('/user/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.public')->scopeBindings();

Route::get('/forum', [App\Http\Controllers\ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{forum}', [App\Http\Controllers\ForumController::class, 'showForum'])->name('forum.show');
Route::get('/forum/{forum}/thread/create', [App\Http\Controllers\ForumController::class, 'createThread'])->name('forum.thread.create');
Route::post('/forum/{forum}/thread', [App\Http\Controllers\ForumController::class, 'storeThread'])->name('forum.thread.store');
Route::get('/forum/thread/{thread}', [App\Http\Controllers\ForumController::class, 'showThread'])->name('forum.thread.show');
Route::post('/forum/thread/{thread}/reply', [App\Http\Controllers\ForumController::class, 'reply'])->name('forum.thread.reply');
Route::middleware('auth')->group(function () {
    Route::get('/forum/thread/{thread}/edit', [App\Http\Controllers\ForumController::class, 'editThread'])->name('forum.thread.edit');
    Route::put('/forum/thread/{thread}', [App\Http\Controllers\ForumController::class, 'updateThread'])->name('forum.thread.update');
    Route::delete('/forum/thread/{thread}', [App\Http\Controllers\ForumController::class, 'destroyThread'])->name('forum.thread.destroy');
    Route::post('/forum/thread/{thread}/pin', [App\Http\Controllers\ForumController::class, 'togglePin'])->name('forum.thread.toggle-pin');
    Route::post('/forum/thread/{thread}/lock', [App\Http\Controllers\ForumController::class, 'toggleLock'])->name('forum.thread.toggle-lock');
    Route::get('/forum/post/{post}/edit', [App\Http\Controllers\ForumController::class, 'editPost'])->name('forum.post.edit');
    Route::put('/forum/post/{post}', [App\Http\Controllers\ForumController::class, 'updatePost'])->name('forum.post.update');
    Route::delete('/forum/post/{post}', [App\Http\Controllers\ForumController::class, 'destroyPost'])->name('forum.post.destroy');
});

// Clan-Module
Route::get('/clan/teams', [App\Http\Controllers\ClanTeamController::class, 'index'])->name('clan-teams.index');
Route::get('/clan/teams/{clanTeam:slug}', [App\Http\Controllers\ClanTeamController::class, 'show'])->name('clan-teams.show')->scopeBindings();
Route::get('/clan/bank', [App\Http\Controllers\ClanBankController::class, 'index'])->name('clan-bank.index');
Route::get('/clan/treasury', [App\Http\Controllers\ClanTreasuryController::class, 'index'])->name('clan-treasury.index');
Route::get('/clan/rules', [App\Http\Controllers\ClanRuleController::class, 'index'])->name('clan-rules.index');
Route::get('/clan/leaderboard', [App\Http\Controllers\ClanLeaderboardController::class, 'index'])->name('clan-leaderboard.index');
Route::get('/clan/leaderboard/{category:slug}', [App\Http\Controllers\ClanLeaderboardController::class, 'category'])->name('clan-leaderboard.category')->scopeBindings();
Route::get('/clan/documents', [App\Http\Controllers\ClanDocumentController::class, 'index'])->name('clan-documents.index');
Route::get('/clan/documents/{document}', [App\Http\Controllers\ClanDocumentController::class, 'show'])->name('clan-documents.show');
Route::get('/clan/feedback', [App\Http\Controllers\ClanFeedbackController::class, 'index'])->name('clan-feedback.index');
Route::post('/clan/feedback', [App\Http\Controllers\ClanFeedbackController::class, 'store'])->middleware('throttle:5,1')->name('clan-feedback.store');
Route::get('/clan/announcements', [App\Http\Controllers\ClanAnnouncementController::class, 'index'])->name('clan-announcements.index');
Route::get('/clan/achievements', [App\Http\Controllers\ClanAchievementController::class, 'index'])->name('clan-achievements.index');

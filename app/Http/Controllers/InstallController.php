<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Plugin;
use App\Support\Installer;
use App\Support\ModuleManager;
use App\Support\PluginManager;
use App\Support\ThemeManager;
use Database\Seeders\LegalPagesSeeder;
use Database\Seeders\MenuItemSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Validation\Rule;

class InstallController extends Controller
{
    public const TOTAL_STEPS = 7;

    public function index(Request $request)
    {
        $this->ensureEnvAndKey();

        $step = (int) $request->get('step', 1);
        if ($step < 1 || $step > self::TOTAL_STEPS) {
            $step = 1;
        }

        try {
            $requirements = Installer::checkRequirements();
        } catch (\Throwable $e) {
            $requirements = [
                'ok' => false,
                'errors' => ['Prüfung fehlgeschlagen: '.$e->getMessage()],
                'php' => true,
                'php_version' => PHP_VERSION,
                'php_sapi' => PHP_SAPI,
                'php_ini' => php_ini_loaded_file(),
                'extension_dir' => (string) ini_get('extension_dir'),
                'extensions' => [],
                'extension_status' => [],
                'recommended_missing' => [],
                'writable' => [],
                'writable_status' => [],
                'optional' => [],
                'composer_available' => false,
                'vendor_ok' => false,
                'mysql_driver' => false,
            ];
        }

        $themes = [];
        $modules = [];
        $plugins = [];
        try {
            $themes = app(ThemeManager::class)->discover(selectableOnly: true);
            ksort($themes);
        } catch (\Throwable $e) {
            // ignore until vendor/autoload is ready
        }

        if ($step >= 7) {
            try {
                $modules = app(ModuleManager::class)->discover();
                ksort($modules);
            } catch (\Throwable $e) {
                $modules = [];
            }
            try {
                $plugins = app(PluginManager::class)->discover();
                ksort($plugins);
            } catch (\Throwable $e) {
                $plugins = [];
            }
        }

        $percent = (int) round((($step) / self::TOTAL_STEPS) * 100);

        return view('install.index', [
            'step' => $step,
            'totalSteps' => self::TOTAL_STEPS,
            'percent' => $percent,
            'requirements' => $requirements,
            'suggestedAppUrl' => rtrim($request->getSchemeAndHttpHost().$request->getBaseUrl(), '/'),
            'themes' => $themes,
            'modules' => $modules,
            'plugins' => $plugins,
            // Nur Pakete, die wirklich unter modules/ bzw. plugins/ liegen
            'defaultModules' => array_keys($modules),
            'defaultPlugins' => array_keys($plugins),
            'mailTested' => (bool) session('install.mail_tested', false),
        ]);
    }

    /** composer install aus dem Web-Installer (Schritt 1). */
    public function dependencies()
    {
        $result = $this->tryComposerInstall();

        if ($result === true) {
            return redirect()->route('install.index', ['step' => 1])
                ->with('success', __('install.composer_ok'));
        }

        return redirect()->route('install.index', ['step' => 1])
            ->with('error', __('install.composer_failed', ['error' => $result ?: 'unbekannt']));
    }

    public function database(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
            'db_create' => 'nullable|boolean',
            'db_fresh' => 'nullable|boolean',
        ]);

        $host = $request->input('db_host');
        $port = $request->input('db_port');
        $database = $request->input('db_database');
        $username = $request->input('db_username');
        $password = (string) $request->input('db_password');
        $createDb = $request->boolean('db_create');
        $freshDb = $request->boolean('db_fresh');

        $blocked = ['mysql', 'information_schema', 'performance_schema', 'sys'];
        if ($freshDb && in_array(strtolower($database), $blocked, true)) {
            return back()->withInput()->withErrors([
                'db' => __('install.db_fresh_blocked', ['name' => $database]),
            ]);
        }

        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port}",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $safeName = str_replace('`', '``', $database);

            if ($freshDb) {
                $pdo->exec("DROP DATABASE IF EXISTS `{$safeName}`");
                $pdo->exec("CREATE DATABASE `{$safeName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                Installer::clearInstalled();
                session()->forget([
                    'install.site_name',
                    'install.app_url',
                    'install.theme',
                    'install.locale',
                    'install.theme_default_mode',
                    'install.mail_tested',
                    'install.mail_hash',
                    'install.mail_test_to',
                ]);
            } elseif ($createDb) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            new \PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['db' => 'Verbindung fehlgeschlagen: '.$e->getMessage()]);
        }

        $this->writeEnvDatabase($host, $port, $database, $username, $password);
        $this->writeEnvValues(['QUEUE_CONNECTION' => 'sync']);

        $redirect = redirect()->route('install.index', ['step' => 3]);
        if ($freshDb) {
            $redirect->with('success', __('install.db_fresh_ok'));
        }

        return $redirect;
    }

    public function migrate()
    {
        try {
            $exitCode = Artisan::call('migrate', ['--force' => true]);
            if ($exitCode !== 0) {
                $output = trim(Artisan::output());

                return redirect()->route('install.index', ['step' => 3])
                    ->with('error', 'Migration fehlgeschlagen'.($output !== '' ? ': '.$output : '.'));
            }
        } catch (\Throwable $e) {
            return redirect()->route('install.index', ['step' => 3])
                ->with('error', 'Migration fehlgeschlagen: '.$e->getMessage());
        }

        return redirect()->route('install.index', ['step' => 4])
            ->with('success', __('install.migrate_ok'));
    }

    public function site(Request $request)
    {
        $themeIds = array_keys(app(ThemeManager::class)->discover(selectableOnly: true));

        $request->validate([
            'site_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'theme' => ['required', 'string', Rule::in($themeIds)],
            'app_locale' => ['required', Rule::in(['de', 'en'])],
            'theme_default_mode' => ['required', Rule::in(['dark', 'light', 'system'])],
        ]);

        $siteName = trim((string) $request->input('site_name'));
        $appUrl = rtrim(trim((string) $request->input('app_url')), '/');
        $theme = (string) $request->input('theme');
        $locale = (string) $request->input('app_locale');
        $mode = (string) $request->input('theme_default_mode');

        $quoted = $this->quoteEnvValue($siteName);
        $this->writeEnvValues([
            'APP_NAME' => $quoted,
            'CLAN_NAME' => $quoted,
            'APP_URL' => $appUrl,
            'APP_LOCALE' => $locale,
            'APP_FALLBACK_LOCALE' => $locale === 'de' ? 'en' : 'de',
            'CLAN_THEME' => $theme,
            'QUEUE_CONNECTION' => 'sync',
        ]);

        // Zwischenspeichern für Schritt 5 (falls Settings-Tabelle schon existiert)
        try {
            set_setting('site_name', $siteName);
            set_setting('theme', $theme);
            app(ThemeManager::class)->setDefaultThemeMode($mode);
            $this->applyThemeColors($theme);
        } catch (\Throwable $e) {
            // Settings ggf. noch nicht migriert – finish setzt erneut
        }

        session([
            'install.site_name' => $siteName,
            'install.app_url' => $appUrl,
            'install.theme' => $theme,
            'install.locale' => $locale,
            'install.theme_default_mode' => $mode,
        ]);

        return redirect()->route('install.index', ['step' => 5]);
    }

    public function discord(Request $request)
    {
        $request->validate([
            'discord_invite_url' => 'nullable|url|max:500',
            'discord_webhook_url' => 'nullable|url|max:500',
            'discord_bot_enabled' => 'nullable|boolean',
            'discord_bot_token' => 'nullable|string|max:255',
            'discord_bot_api_key' => 'nullable|string|max:255',
            'discord_shop_webhook_url' => 'nullable|url|max:500',
            'discord_events_webhook_url' => 'nullable|url|max:500',
            'discord_skip' => 'nullable|boolean',
        ]);

        $skip = $request->boolean('discord_skip');
        $botEnabled = $request->boolean('discord_bot_enabled');
        $invite = trim((string) $request->input('discord_invite_url', ''));
        $webhook = trim((string) $request->input('discord_webhook_url', ''));
        $token = trim((string) $request->input('discord_bot_token', ''));
        $apiKey = trim((string) $request->input('discord_bot_api_key', ''));
        $shopWebhook = trim((string) $request->input('discord_shop_webhook_url', ''));
        $eventsWebhook = trim((string) $request->input('discord_events_webhook_url', ''));

        if (! $skip && $botEnabled && $token === '') {
            return back()->withInput()->withErrors([
                'discord_bot_token' => __('install.discord_token_required'),
            ]);
        }

        if ($botEnabled && $apiKey === '') {
            $apiKey = bin2hex(random_bytes(24));
        }

        $this->writeEnvValues([
            'DISCORD_INVITE_URL' => $invite,
            'DISCORD_WEBHOOK_URL' => $webhook,
            'DISCORD_BOT_ENABLED' => $botEnabled ? 'true' : 'false',
            'DISCORD_BOT_TOKEN' => $token,
            'DISCORD_BOT_API_KEY' => $apiKey,
            'DISCORD_SHOP_WEBHOOK_URL' => $shopWebhook,
            'DISCORD_EVENTS_WEBHOOK_URL' => $eventsWebhook,
        ]);

        try {
            if ($invite !== '') {
                set_setting('discord_invite_url', $invite);
            }
            if ($webhook !== '') {
                set_setting('discord_webhook_url', $webhook);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        session([
            'install.discord_invite_url' => $invite,
            'install.discord_bot_enabled' => $botEnabled,
            'install.discord_bot_api_key' => $apiKey,
        ]);

        return redirect()->route('install.index', ['step' => 6])
            ->with('success', $skip ? __('install.discord_skipped') : __('install.discord_saved'));
    }

    public function mailTest(Request $request)
    {
        $request->validate([
            'mail_mailer' => ['required', Rule::in(['smtp', 'log', 'sendmail'])],
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_test_to' => 'required|email|max:255',
        ]);

        $mailer = (string) $request->input('mail_mailer');
        $host = trim((string) $request->input('mail_host', '127.0.0.1'));
        $port = trim((string) $request->input('mail_port', '587'));
        $username = trim((string) $request->input('mail_username', ''));
        $password = (string) $request->input('mail_password', '');
        $encryption = (string) $request->input('mail_encryption', 'tls');
        $fromAddress = trim((string) $request->input('mail_from_address'));
        $fromName = trim((string) $request->input('mail_from_name', config('app.name', 'ZerroCMS')));
        $testTo = trim((string) $request->input('mail_test_to'));

        if ($mailer === 'smtp' && $host === '') {
            return back()->withInput()->withErrors(['mail_host' => __('install.mail_host_required')]);
        }

        $this->persistMailEnv($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName);
        $this->applyMailRuntimeConfig($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                __('install.mail_test_body', ['app' => config('app.name', 'ZerroCMS'), 'url' => config('app.url')]),
                function ($message) use ($testTo, $fromAddress, $fromName) {
                    $message->to($testTo)
                        ->from($fromAddress, $fromName !== '' ? $fromName : null)
                        ->subject(__('install.mail_test_subject'));
                }
            );
        } catch (\Throwable $e) {
            session()->forget('install.mail_tested');

            return back()->withInput()->withErrors([
                'mail_test' => __('install.mail_test_failed', ['error' => $e->getMessage()]),
            ]);
        }

        session([
            'install.mail_tested' => true,
            'install.mail_test_to' => $testTo,
            'install.mail_mailer' => $mailer,
            'install.mail_hash' => $this->mailSettingsHash($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName),
        ]);

        return back()->withInput()->with('success', __('install.mail_test_ok', ['email' => $testTo]));
    }

    public function mail(Request $request)
    {
        $request->validate([
            'mail_mailer' => ['required', Rule::in(['smtp', 'log', 'sendmail'])],
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $mailer = (string) $request->input('mail_mailer');
        $host = trim((string) $request->input('mail_host', '127.0.0.1'));
        $port = trim((string) $request->input('mail_port', '587'));
        $username = trim((string) $request->input('mail_username', ''));
        $password = (string) $request->input('mail_password', '');
        $encryption = (string) $request->input('mail_encryption', 'tls');
        $fromAddress = trim((string) $request->input('mail_from_address'));
        $fromName = trim((string) $request->input('mail_from_name', config('app.name', 'ZerroCMS')));

        $hash = $this->mailSettingsHash($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName);
        if (! session('install.mail_tested') || session('install.mail_hash') !== $hash) {
            return back()->withInput()->withErrors([
                'mail_test' => __('install.mail_test_required'),
            ]);
        }

        $this->persistMailEnv($mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName);

        session([
            'install.mail_mailer' => $mailer,
            'install.mail_from_address' => $fromAddress,
        ]);

        return redirect()->route('install.index', ['step' => 7])
            ->with('success', __('install.mail_saved'));
    }

    protected function mailSettingsHash(
        string $mailer,
        string $host,
        string $port,
        string $username,
        string $password,
        string $encryption,
        string $fromAddress,
        string $fromName
    ): string {
        return hash('sha256', implode('|', [$mailer, $host, $port, $username, $password, $encryption, $fromAddress, $fromName]));
    }

    protected function persistMailEnv(
        string $mailer,
        string $host,
        string $port,
        string $username,
        string $password,
        string $encryption,
        string $fromAddress,
        string $fromName
    ): void {
        $scheme = $encryption === '' ? 'null' : $encryption;
        $this->writeEnvValues([
            'MAIL_MAILER' => $mailer,
            'MAIL_HOST' => $host !== '' ? $host : '127.0.0.1',
            'MAIL_PORT' => $port !== '' ? $port : '587',
            'MAIL_USERNAME' => $username !== '' ? $username : 'null',
            'MAIL_PASSWORD' => $password !== '' ? $this->quoteEnvValue($password) : 'null',
            'MAIL_SCHEME' => $scheme,
            'MAIL_FROM_ADDRESS' => $this->quoteEnvValue($fromAddress),
            'MAIL_FROM_NAME' => $this->quoteEnvValue($fromName !== '' ? $fromName : 'ZerroCMS'),
        ]);
    }

    protected function applyMailRuntimeConfig(
        string $mailer,
        string $host,
        string $port,
        string $username,
        string $password,
        string $encryption,
        string $fromAddress,
        string $fromName
    ): void {
        try {
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
            // ignore
        }

        config([
            'mail.default' => $mailer,
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) ($port !== '' ? $port : 587),
            'mail.mailers.smtp.username' => $username !== '' ? $username : null,
            'mail.mailers.smtp.password' => $password !== '' ? $password : null,
            'mail.mailers.smtp.scheme' => $encryption !== '' ? $encryption : null,
            'mail.mailers.smtp.encryption' => $encryption !== '' ? $encryption : null,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName !== '' ? $fromName : 'ZerroCMS',
        ]);
    }

    public function finish(Request $request)
    {
        // Apache/mod_php: npm-Build sonst oft bei 30s (WindowsPipes) tot.
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);
        ignore_user_abort(true);

        $discoveredModules = array_keys(app(ModuleManager::class)->discover());
        $discoveredPlugins = array_keys(app(PluginManager::class)->discover());
        $themeIds = array_keys(app(ThemeManager::class)->discover(selectableOnly: true));

        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'theme' => ['nullable', 'string', Rule::in($themeIds)],
            'modules' => 'nullable|array',
            'modules.*' => ['string', Rule::in($discoveredModules)],
            'plugins' => 'nullable|array',
            'plugins.*' => ['string', Rule::in($discoveredPlugins)],
            'build_assets' => 'nullable|boolean',
        ]);

        $name = $request->input('admin_name');
        $email = $request->input('admin_email');
        $password = $request->input('admin_password');
        $siteName = (string) (session('install.site_name') ?: site_name());
        $appUrl = (string) (session('install.app_url') ?: config('app.url'));
        $theme = (string) ($request->input('theme') ?: session('install.theme') ?: config('clan.theme', 'pax-dei'));
        $locale = (string) (session('install.locale') ?: 'de');
        $mode = (string) (session('install.theme_default_mode') ?: 'dark');
        $enabledModules = $request->input('modules', $discoveredModules);
        $enabledPlugins = $request->input('plugins', $discoveredPlugins);
        // Standard: aus – CDN-Fallback reicht; npm unter Apache oft >30s.
        $buildAssets = $request->boolean('build_assets', false);
        $postSteps = [];

        try {
            if ($siteName !== '') {
                $quoted = $this->quoteEnvValue($siteName);
                $this->writeEnvValues([
                    'APP_NAME' => $quoted,
                    'CLAN_NAME' => $quoted,
                    'CLAN_THEME' => $theme,
                    'APP_LOCALE' => $locale,
                    'APP_FALLBACK_LOCALE' => $locale === 'de' ? 'en' : 'de',
                ]);
            }
            if ($appUrl !== '') {
                $this->writeEnvValues(['APP_URL' => $appUrl]);
            }
            $this->writeEnvValues(['QUEUE_CONNECTION' => 'sync']);
            $this->writeEnvValues([
                'SESSION_DRIVER' => 'database',
                'SESSION_ENCRYPT' => 'true',
                'CACHE_STORE' => 'database',
                'HASH_VERIFY' => 'false',
            ]);
            if ($appUrl !== '' && str_starts_with($appUrl, 'https://')) {
                $this->writeEnvValues(['SESSION_SECURE_COOKIE' => 'true']);
            }

            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
                $postSteps[] = __('install.done_key');
            }

            $user = \App\Models\User::query()->where('email', $email)->first();
            if ($user) {
                $user->forceFill([
                    'name' => $name,
                    'password' => $password,
                ])->save();
            } else {
                $user = \App\Models\User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);
            }
            Artisan::call('db:seed', ['--force' => true, '--class' => RolePermissionSeeder::class]);
            Artisan::call('db:seed', ['--force' => true, '--class' => RankSeeder::class]);
            Artisan::call('db:seed', ['--force' => true, '--class' => MenuItemSeeder::class]);
            Artisan::call('db:seed', ['--force' => true, '--class' => LegalPagesSeeder::class]);
            if (! $user->hasRole('super-admin')) {
                $user->assignRole('super-admin');
            }
            $postSteps[] = __('install.done_seed');

            set_setting('site_name', $siteName !== '' ? $siteName : 'ZerroCMS');
            set_setting('theme', $theme);
            app(ThemeManager::class)->setDefaultThemeMode($mode);
            $this->applyThemeColors($theme);
            $postSteps[] = __('install.done_theme', ['theme' => $theme]);

            $moduleCount = $this->enableModules(is_array($enabledModules) ? $enabledModules : []);
            $pluginCount = $this->enablePlugins(is_array($enabledPlugins) ? $enabledPlugins : []);
            $postSteps[] = __('install.done_modules', ['count' => $moduleCount]);
            $postSteps[] = __('install.done_plugins', ['count' => $pluginCount]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['finish' => $e->getMessage()]);
        }

        try {
            Artisan::call('storage:link', ['--force' => true]);
            $postSteps[] = __('install.done_storage_link');
        } catch (\Throwable $e) {
            $postSteps[] = __('install.done_storage_link_skipped', ['error' => $e->getMessage()]);
        }

        // Installation zuerst abschließen – npm darf danach scheitern/timeouten.
        if (! Installer::markInstalled()) {
            return back()->withInput()->withErrors(['finish' => __('install.storage_failed')]);
        }

        if ($buildAssets) {
            try {
                $buildResult = $this->tryBuildFrontendAssets();
                if ($buildResult === true) {
                    $postSteps[] = __('install.done_assets');
                } elseif ($buildResult === null) {
                    $postSteps[] = __('install.done_assets_skipped');
                } else {
                    $postSteps[] = __('install.done_assets_failed', ['error' => $buildResult]);
                }
            } catch (\Throwable $e) {
                $postSteps[] = __('install.done_assets_failed', ['error' => $e->getMessage()]);
            }
        } else {
            $postSteps[] = __('install.done_assets_skipped');
        }

        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
        } catch (\Throwable $e) {
            // ignore
        }

        session()->forget([
            'install.site_name',
            'install.app_url',
            'install.theme',
            'install.locale',
            'install.theme_default_mode',
            'install.mail_tested',
            'install.mail_test_to',
            'install.mail_mailer',
            'install.mail_from_address',
            'install.discord_invite_url',
            'install.discord_bot_enabled',
            'install.discord_bot_api_key',
        ]);

        $response = redirect()->route('home')->with([
            'install_complete' => true,
            'install_post_steps' => $postSteps,
        ]);

        // Alte Light/Dark-Cookies verwerfen, damit die Installer-Wahl gilt
        foreach (['zerrocms_theme_mode', 'zerrocms_theme_user_choice', 'zerrocms_theme_mode_rev'] as $cookieName) {
            $response->withCookie(cookie()->forget($cookieName));
        }

        return $response;
    }

    protected function applyThemeColors(string $theme): void
    {
        $themes = app(ThemeManager::class)->discover();
        $colors = $themes[$theme]['colors'] ?? [];
        foreach ($colors as $key => $value) {
            if (is_string($key) && is_string($value) && $value !== '') {
                set_setting('theme_'.$key, $value);
            }
        }
    }

    /**
     * @param  list<string>  $names
     */
    protected function enableModules(array $names): int
    {
        $discovered = app(ModuleManager::class)->discover();
        $count = 0;
        foreach ($names as $name) {
            if (! isset($discovered[$name])) {
                continue;
            }
            Module::updateOrCreate(['name' => $name], ['enabled' => true]);
            $count++;
        }

        return $count;
    }

    /**
     * @param  list<string>  $names
     */
    protected function enablePlugins(array $names): int
    {
        $discovered = app(PluginManager::class)->discover();
        $count = 0;
        $order = 0;
        foreach ($names as $name) {
            if (! isset($discovered[$name])) {
                continue;
            }
            Plugin::updateOrCreate(
                ['name' => $name],
                ['enabled' => true, 'order' => $order++]
            );
            $count++;
        }

        return $count;
    }

    protected function ensureEnvAndKey(): void
    {
        try {
            $envPath = base_path('.env');
            if (! File::exists($envPath) && File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
            }
            if (File::exists($envPath) && empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Schreibrechte o. Ä. – Assistent zeigt Anforderungen
        }
    }

    /**
     * @return true|string
     */
    protected function tryComposerInstall(): true|string
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $cwd = base_path();
        $phar = base_path('composer.phar');

        if (! File::exists($phar) && ! $this->commandAvailable('composer')) {
            $dl = $this->downloadComposerPhar($phar);
            if ($dl !== true) {
                return 'composer.phar fehlt und Download fehlgeschlagen: '.$dl;
            }
        }

        $commands = [];
        if ($this->commandAvailable('composer')) {
            $commands[] = ['composer', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'];
        }
        if (File::exists($phar)) {
            $commands[] = ['php', 'composer.phar', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'];
        }

        if ($commands === []) {
            return __('install.composer_not_found');
        }

        $lastError = '';
        foreach ($commands as $cmd) {
            try {
                $result = Process::path($cwd)->timeout(900)->run($cmd);
                if ($result->successful() && File::exists(base_path('vendor/autoload.php'))) {
                    return true;
                }
                $lastError = trim($result->errorOutput() ?: $result->output()) ?: 'composer install failed';
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return $lastError;
    }

    /**
     * @return true|string
     */
    protected function downloadComposerPhar(string $target): true|string
    {
        $url = 'https://getcomposer.org/download/latest-stable/composer.phar';
        try {
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 120,
                    CURLOPT_USERAGENT => 'ZerroCMS-Installer',
                ]);
                $data = curl_exec($ch);
                $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err = curl_error($ch);
                curl_close($ch);
                if ($data === false || $code >= 400) {
                    return $err !== '' ? $err : "HTTP {$code}";
                }
            } else {
                $data = file_get_contents($url);
                if ($data === false) {
                    return 'Download fehlgeschlagen';
                }
            }
            if (strlen($data) < 100000) {
                return 'Download ungültig';
            }
            File::put($target, $data);

            return true;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param  array<string, string>  $lines
     */
    protected function writeEnvValues(array $lines): void
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
            } else {
                File::put($envPath, "APP_KEY=\n");
            }
        }

        $content = File::get($envPath);
        foreach ($lines as $key => $value) {
            if (preg_match('/^'.preg_quote($key, '/').'=/m', $content)) {
                $content = preg_replace('/^('.preg_quote($key, '/').')=.*/m', '$1='.$value, $content, 1);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }
        File::put($envPath, $content);
    }

    protected function quoteEnvValue(string $value): string
    {
        if ($value === '' || preg_match('/\s|#|"|\'/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }

    protected function writeEnvDatabase(string $host, string $port, string $database, string $username, string $password): void
    {
        $this->writeEnvValues([
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $password,
        ]);

        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }
        Artisan::call('config:clear');
    }

    /**
     * @return true|null|string true = ok, null = npm fehlt / bereits gebaut, string = Fehlertext
     */
    protected function tryBuildFrontendAssets(): true|string|null
    {
        if (File::exists(public_path('build/manifest.json'))) {
            return true;
        }

        if (! $this->commandAvailable('npm')) {
            return null;
        }

        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $cwd = base_path();
        $installCmd = File::exists(base_path('package-lock.json')) ? ['npm', 'ci'] : ['npm', 'install'];

        try {
            // Kurzes Process-Timeout: besser Skip als Apache-500.
            $install = Process::path($cwd)->timeout(120)->run($installCmd);
            if (! $install->successful()) {
                return trim($install->errorOutput() ?: $install->output()) ?: 'npm install failed';
            }

            $build = Process::path($cwd)->timeout(180)->run(['npm', 'run', 'build']);
            if (! $build->successful()) {
                return trim($build->errorOutput() ?: $build->output()) ?: 'npm run build failed';
            }
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return File::exists(public_path('build/manifest.json')) ? true : 'npm build produced no manifest';
    }

    protected function commandAvailable(string $command): bool
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $result = Process::timeout(10)->run(['where', $command]);
            } else {
                $result = Process::timeout(10)->run(['which', $command]);
            }

            return $result->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}

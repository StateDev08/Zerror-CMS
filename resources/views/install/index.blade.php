@extends('install.layout')

@section('content')
    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-100 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif

    @if($step === 1)
        <div class="space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('install.requirements') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.step1_intro') }}</p>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600 space-y-1">
                <p><span class="font-medium">PHP:</span> {{ $requirements['php_version'] ?? PHP_VERSION }} ({{ $requirements['php_sapi'] ?? PHP_SAPI }})</p>
                @if(!empty($requirements['php_ini']))
                    <p><span class="font-medium">php.ini:</span> <code class="break-all">{{ $requirements['php_ini'] }}</code></p>
                @endif
                @if(!empty($requirements['extension_dir']))
                    <p><span class="font-medium">extension_dir:</span> <code class="break-all">{{ $requirements['extension_dir'] }}</code></p>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('install.checklist_php') }}</h3>
                <ul class="text-sm space-y-1">
                    <li class="{{ !empty($requirements['php']) ? 'text-green-700' : 'text-red-600' }}">
                        {{ !empty($requirements['php']) ? '✓' : '✗' }}
                        {{ __('install.requirements_php', ['version' => $requirements['php_version'] ?? PHP_VERSION]) }}
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('install.checklist_extensions') }}</h3>
                <ul class="text-sm grid grid-cols-1 sm:grid-cols-2 gap-1">
                    @foreach(($requirements['extension_status'] ?? []) as $ext => $loaded)
                        <li class="{{ $loaded ? 'text-green-700' : 'text-red-600 font-medium' }}">
                            {{ $loaded ? '✓' : '✗' }} {{ $ext }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('install.checklist_writable') }}</h3>
                <ul class="text-sm space-y-1">
                    @foreach(($requirements['writable_status'] ?? []) as $path => $ok)
                        <li class="{{ $ok ? 'text-green-700' : 'text-red-600' }}">
                            {{ $ok ? '✓' : '✗' }} {{ $path }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('install.checklist_other') }}</h3>
                <ul class="text-sm space-y-1">
                    <li class="{{ !empty($requirements['vendor_ok']) ? 'text-green-700' : 'text-red-600' }}">
                        {{ !empty($requirements['vendor_ok']) ? '✓' : '✗' }}
                        {{ __('install.requirements_vendor') }}
                    </li>
                    <li class="text-green-700">✓ {{ __('install.requirements_env') }}</li>
                </ul>
            </div>

            @if(!empty($requirements['optional']))
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('install.checklist_optional') }}</h3>
                    <ul class="text-sm text-slate-500 space-y-1">
                        @foreach($requirements['optional'] as $hint)
                            <li>• {{ $hint }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($requirements['ok']))
                <p class="text-green-600 text-sm font-medium">{{ __('install.requirements_ok') }}</p>
                @if(!empty($requirements['composer_available']))
                    <form action="{{ route('install.dependencies') }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="text-sm text-amber-700 hover:underline">{{ __('install.run_composer_again') }}</button>
                    </form>
                @endif
                <a href="{{ route('install.index', ['step' => 2]) }}" class="inline-block mt-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.next_database') }}</a>
            @else
                <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                    <p class="text-sm font-medium text-red-800 mb-2">{{ __('install.fix_errors') }}</p>
                    <ul class="text-sm text-red-700 space-y-1.5">
                        @foreach($requirements['errors'] as $err)
                            <li>✗ {{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @if(empty($requirements['vendor_ok']))
                    <form action="{{ route('install.dependencies') }}" method="POST" class="mt-2" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='Bitte warten…';">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.run_composer') }}</button>
                    </form>
                    <p class="text-xs text-slate-500 mt-2">Oder zuerst: <a href="/preinstall.php" class="text-amber-700 underline">Vorbereitung ohne SSH</a></p>
                @endif
                <a href="{{ route('install.index', ['step' => 1]) }}" class="inline-block mt-2 text-sm text-amber-700 hover:underline">{{ __('install.recheck') }}</a>
            @endif
        </div>

    @elseif($step === 2)
        <form action="{{ route('install.database') }}" method="POST" class="space-y-4" data-loading="{{ __('install.loading_db') }}">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.database') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.step2_intro') }}</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_host') }}</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_port') }}</label>
                <input type="text" name="db_port" value="{{ old('db_port', '3306') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_database') }}</label>
                <input type="text" name="db_database" value="{{ old('db_database', 'zerrocms') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_username') }}</label>
                <input type="text" name="db_username" value="{{ old('db_username', 'root') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.db_password') }}</label>
                <input type="password" name="db_password" value="{{ old('db_password') }}" class="w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <label class="flex items-start gap-2 text-sm text-slate-700">
                <input type="checkbox" name="db_create" value="1" class="mt-1 rounded border-slate-300" @checked(old('db_create', true))>
                <span>{{ __('install.db_create') }}</span>
            </label>
            <label class="flex items-start gap-2 text-sm text-red-700">
                <input type="checkbox" name="db_fresh" value="1" class="mt-1 rounded border-slate-300" @checked(old('db_fresh'))>
                <span>{{ __('install.db_fresh') }}</span>
            </label>
            <p class="text-slate-500 text-xs">{{ __('install.db_hint') }}</p>
            <p class="text-red-600/80 text-xs">{{ __('install.db_fresh_hint') }}</p>
            <button type="submit" class="w-full mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.test_save') }}</button>
        </form>

    @elseif($step === 3)
        <div class="space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('install.migrate') }}</h2>
            <p class="text-slate-600 text-sm">{{ __('install.migrate_hint') }}</p>
            <a href="{{ route('install.migrate') }}" class="inline-block w-full text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium" data-loading="{{ __('install.loading_migrate') }}">{{ __('install.run_migrate') }}</a>
            <p class="text-xs text-slate-500">{{ __('install.migrate_retry_hint') }}</p>
        </div>

    @elseif($step === 4)
        <form action="{{ route('install.site') }}" method="POST" class="space-y-4" data-loading="{{ __('install.loading_save') }}">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.site_theme') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.step4_intro') }}</p>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.site_name') }}</label>
                <input type="text" name="site_name" value="{{ old('site_name', session('install.site_name', 'ZerroCMS')) }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.app_url') }}</label>
                <input type="url" name="app_url" value="{{ old('app_url', session('install.app_url', $suggestedAppUrl ?? url('/'))) }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.app_locale') }}</label>
                <select name="app_locale" class="w-full rounded border border-slate-300 px-3 py-2">
                    <option value="de" @selected(old('app_locale', session('install.locale', 'de')) === 'de')">Deutsch</option>
                    <option value="en" @selected(old('app_locale', session('install.locale', 'de')) === 'en')">English</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.theme_default_mode') }}</label>
                <select name="theme_default_mode" class="w-full rounded border border-slate-300 px-3 py-2">
                    <option value="dark" @selected(old('theme_default_mode', session('install.theme_default_mode', 'dark')) === 'dark')">{{ __('install.mode_dark') }}</option>
                    <option value="light" @selected(old('theme_default_mode', session('install.theme_default_mode', 'dark')) === 'light')">{{ __('install.mode_light') }}</option>
                    <option value="system" @selected(old('theme_default_mode', session('install.theme_default_mode', 'dark')) === 'system')">{{ __('install.mode_system') }}</option>
                </select>
            </div>

            <fieldset>
                <legend class="block text-sm font-medium text-slate-700 mb-2">{{ __('install.theme_pick') }}</legend>
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @forelse($themes as $id => $theme)
                        @php
                            $primary = $theme['colors']['primary'] ?? '#c9a227';
                            $accent = $theme['colors']['accent'] ?? '#3b82f6';
                            $checked = old('theme', session('install.theme', 'pax-dei')) === $id;
                        @endphp
                        <label class="flex gap-3 items-start rounded-lg border px-3 py-2 cursor-pointer {{ $checked ? 'border-amber-500 bg-amber-50' : 'border-slate-200 hover:border-slate-300' }}">
                            <input type="radio" name="theme" value="{{ $id }}" class="mt-1" @checked($checked) required>
                            <span class="flex-1 min-w-0">
                                <span class="flex items-center gap-2">
                                    <span class="inline-flex gap-1 shrink-0">
                                        <span class="w-3 h-3 rounded-full border border-slate-300" style="background: {{ $primary }}"></span>
                                        <span class="w-3 h-3 rounded-full border border-slate-300" style="background: {{ $accent }}"></span>
                                    </span>
                                    <span class="font-medium text-slate-800">{{ $theme['label'] ?? $id }}</span>
                                </span>
                                @if(!empty($theme['description']))
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ $theme['description'] }}</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-red-600">{{ __('install.themes_missing') }}</p>
                    @endforelse
                </div>
            </fieldset>

            <button type="submit" class="w-full mt-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium" data-loading="{{ __('install.loading_save') }}">{{ __('install.next_discord') }}</button>
        </form>

    @elseif($step === 5)
        <form action="{{ route('install.discord') }}" method="POST" class="space-y-4" data-loading="{{ __('install.loading_save') }}">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.discord_title') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.discord_intro') }}</p>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_invite') }}</label>
                <input type="url" name="discord_invite_url" value="{{ old('discord_invite_url', session('install.discord_invite_url', env('DISCORD_INVITE_URL'))) }}" class="w-full rounded border border-slate-300 px-3 py-2" placeholder="https://discord.gg/…">
                <p class="text-xs text-slate-500 mt-1">{{ __('install.discord_invite_help') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_webhook') }}</label>
                <input type="url" name="discord_webhook_url" value="{{ old('discord_webhook_url', env('DISCORD_WEBHOOK_URL')) }}" class="w-full rounded border border-slate-300 px-3 py-2" placeholder="https://discord.com/api/webhooks/…">
                <p class="text-xs text-slate-500 mt-1">{{ __('install.discord_webhook_help') }}</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 space-y-3">
                <label class="flex items-start gap-2 text-sm text-slate-800 font-medium">
                    <input type="checkbox" name="discord_bot_enabled" value="1" class="mt-1 rounded border-slate-300" @checked(old('discord_bot_enabled', session('install.discord_bot_enabled')))>
                    <span>{{ __('install.discord_bot_enable') }}</span>
                </label>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_bot_token') }}</label>
                    <input type="password" name="discord_bot_token" value="{{ old('discord_bot_token') }}" class="w-full rounded border border-slate-300 px-3 py-2" autocomplete="off">
                    <p class="text-xs text-slate-500 mt-1">{{ __('install.discord_bot_token_help') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_bot_api_key') }}</label>
                    <input type="text" name="discord_bot_api_key" value="{{ old('discord_bot_api_key', session('install.discord_bot_api_key')) }}" class="w-full rounded border border-slate-300 px-3 py-2" placeholder="{{ __('install.discord_bot_api_key_ph') }}">
                    <p class="text-xs text-slate-500 mt-1">{{ __('install.discord_bot_api_key_help') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_shop_webhook') }}</label>
                    <input type="url" name="discord_shop_webhook_url" value="{{ old('discord_shop_webhook_url', env('DISCORD_SHOP_WEBHOOK_URL')) }}" class="w-full rounded border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.discord_events_webhook') }}</label>
                    <input type="url" name="discord_events_webhook_url" value="{{ old('discord_events_webhook_url', env('DISCORD_EVENTS_WEBHOOK_URL')) }}" class="w-full rounded border border-slate-300 px-3 py-2">
                </div>
            </div>

            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="discord_skip" value="1" class="mt-1 rounded border-slate-300" @checked(old('discord_skip'))>
                <span>{{ __('install.discord_skip') }}</span>
            </label>

            <button type="submit" class="w-full mt-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">{{ __('install.next_mail') }}</button>
        </form>

    @elseif($step === 6)
        @php $mailTested = $mailTested ?? false; @endphp
        <div class="space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('install.mail_title') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.mail_intro') }}</p>

            @if($mailTested)
                <div class="p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm">{{ __('install.mail_tested_badge') }}</div>
            @else
                <div class="p-3 rounded-lg bg-amber-50 text-amber-900 text-sm">{{ __('install.mail_test_hint') }}</div>
            @endif

            <form id="mail-settings-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_mailer') }}</label>
                    <select name="mail_mailer" class="w-full rounded border border-slate-300 px-3 py-2">
                        <option value="smtp" @selected(old('mail_mailer', 'smtp') === 'smtp')">SMTP</option>
                        <option value="log" @selected(old('mail_mailer') === 'log')">Log ({{ __('install.mail_mailer_log') }})</option>
                        <option value="sendmail" @selected(old('mail_mailer') === 'sendmail')">Sendmail</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_host') }}</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', '127.0.0.1') }}" class="w-full rounded border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_port') }}</label>
                        <input type="text" name="mail_port" value="{{ old('mail_port', '587') }}" class="w-full rounded border border-slate-300 px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_encryption') }}</label>
                    <select name="mail_encryption" class="w-full rounded border border-slate-300 px-3 py-2">
                        <option value="tls" @selected(old('mail_encryption', 'tls') === 'tls')">TLS</option>
                        <option value="ssl" @selected(old('mail_encryption') === 'ssl')">SSL</option>
                        <option value="" @selected(old('mail_encryption') === '')>{{ __('install.mail_encryption_none') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_username') }}</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username') }}" class="w-full rounded border border-slate-300 px-3 py-2" autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_password') }}</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password') }}" class="w-full rounded border border-slate-300 px-3 py-2" autocomplete="new-password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_from_address') }}</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', 'noreply@'.(parse_url(config('app.url'), PHP_URL_HOST) ?: 'example.com')) }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_from_name') }}</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', session('install.site_name', config('app.name'))) }}" class="w-full rounded border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.mail_test_to') }}</label>
                    <input type="email" name="mail_test_to" value="{{ old('mail_test_to', session('install.mail_test_to')) }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
                    <p class="text-xs text-slate-500 mt-1">{{ __('install.mail_test_to_help') }}</p>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button type="submit" form="mail-settings-form" formaction="{{ route('install.mail-test') }}" formmethod="post" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-medium" data-loading="{{ __('install.loading_mail_test') }}">
                    {{ __('install.mail_send_test') }}
                </button>
                <button type="submit" form="mail-settings-form" formaction="{{ route('install.mail') }}" formmethod="post" class="w-full px-4 py-2 rounded-lg font-medium {{ $mailTested ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-200 text-slate-500 cursor-not-allowed' }}" @if(! $mailTested) disabled @endif data-loading="{{ __('install.loading_save') }}">
                    {{ __('install.next_admin') }}
                </button>
            </div>
        </div>

    @elseif($step === 7)
        <form action="{{ route('install.finish') }}" method="POST" class="space-y-4" data-loading="{{ __('install.loading_finish') }}">
            @csrf
            <h2 class="font-semibold text-slate-800">{{ __('install.admin_user') }}</h2>
            <p class="text-sm text-slate-600">{{ __('install.step7_intro') }}</p>
            <ul class="text-xs text-slate-500 list-disc list-inside space-y-0.5">
                <li>{{ __('install.finish_does_seed') }}</li>
                <li>{{ __('install.finish_does_theme') }}</li>
                <li>{{ __('install.finish_does_modules') }}</li>
                <li>{{ __('install.finish_does_storage') }}</li>
                <li>{{ __('install.finish_does_assets') }}</li>
            </ul>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_name') }}</label>
                <input type="text" name="admin_name" value="{{ old('admin_name', 'Admin') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_email') }}</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_password') }}</label>
                <input type="password" name="admin_password" class="w-full rounded border border-slate-300 px-3 py-2" required minlength="8">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('install.admin_password_confirm') }}</label>
                <input type="password" name="admin_password_confirmation" class="w-full rounded border border-slate-300 px-3 py-2" required>
            </div>
            <p class="text-slate-500 text-xs">{{ __('install.admin_hint') }}</p>

            @if(!empty($modules))
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">{{ __('install.modules_pick') }}</legend>
                    <div class="flex gap-3 text-xs mb-2">
                        <button type="button" class="text-amber-700 hover:underline" onclick="document.querySelectorAll('input[name=\'modules[]\']').forEach(el => el.checked = true)">{{ __('install.select_all') }}</button>
                        <button type="button" class="text-slate-500 hover:underline" onclick="document.querySelectorAll('input[name=\'modules[]\']').forEach(el => el.checked = false)">{{ __('install.select_none') }}</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-40 overflow-y-auto">
                        @foreach($modules as $id => $mod)
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="modules[]" value="{{ $id }}" class="rounded border-slate-300"
                                    @checked(in_array($id, old('modules', $defaultModules), true))>
                                <span>{{ $mod['name'] ?? $id }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @else
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    <p class="font-medium text-slate-800">{{ __('install.modules_none_title') }}</p>
                    <p class="text-xs mt-1">{{ __('install.modules_none_body') }}</p>
                </div>
            @endif

            @if(!empty($plugins))
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">{{ __('install.plugins_pick') }}</legend>
                    <div class="flex gap-3 text-xs mb-2">
                        <button type="button" class="text-amber-700 hover:underline" onclick="document.querySelectorAll('input[name=\'plugins[]\']').forEach(el => el.checked = true)">{{ __('install.select_all') }}</button>
                        <button type="button" class="text-slate-500 hover:underline" onclick="document.querySelectorAll('input[name=\'plugins[]\']').forEach(el => el.checked = false)">{{ __('install.select_none') }}</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-40 overflow-y-auto">
                        @foreach($plugins as $id => $plugin)
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="plugins[]" value="{{ $id }}" class="rounded border-slate-300"
                                    @checked(in_array($id, old('plugins', $defaultPlugins), true))>
                                <span>{{ $plugin['manifest']['label'] ?? $plugin['name'] ?? $id }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @else
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    <p class="font-medium text-slate-800">{{ __('install.plugins_none_title') }}</p>
                    <p class="text-xs mt-1">{{ __('install.plugins_none_body') }}</p>
                </div>
            @endif

            <label class="flex items-start gap-2 text-sm text-slate-700">
                <input type="checkbox" name="build_assets" value="1" class="mt-1 rounded border-slate-300" @checked(old('build_assets', false))>
                <span>{{ __('install.build_assets') }}</span>
            </label>
            <p class="text-xs text-slate-500">{{ __('install.build_assets_hint') }}</p>

            <button type="submit" class="w-full mt-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">{{ __('install.finish') }}</button>
        </form>

    @else
        <p class="text-slate-600">{{ __('install.invalid_step') }}</p>
        <a href="{{ route('install.index', ['step' => 1]) }}" class="text-amber-600 hover:underline">{{ __('install.back') }}</a>
    @endif
@endsection

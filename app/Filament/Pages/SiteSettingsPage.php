<?php

namespace App\Filament\Pages;

use App\Filament\Forms\CmsRichEditor;
use App\Models\Setting;
use App\Support\SiteContent;
use App\Support\SiteMedia;
use App\Support\UploadLimits;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * @property-read Schema $form
 */
class SiteSettingsPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;
    use WithFileUploads;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Einstellungen';

    protected static ?string $title = 'Einstellungen';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.site-settings';

    public string $activeTab = 'general';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    /**
     * Startseiten-Felder (Filament-Form / CmsRichEditor).
     *
     * @var array<string, mixed>
     */
    public array $home = [];

    /** @var TemporaryUploadedFile|null */
    public $logoUpload = null;

    /** @var TemporaryUploadedFile|null */
    public $bannerUpload = null;

    public bool $logoRemove = false;

    public bool $bannerRemove = false;

    /**
     * @var list<string>
     */
    public const TABS = [
        'general',
        'media',
        'home',
        'slider',
        'contact',
        'footer',
        'integrations',
        'cookies',
        'seo',
        'access',
        'uploads',
    ];

    protected static function cmsPagePermission(): string
    {
        return 'manage_settings';
    }

    public function mount(): void
    {
        $this->data = $this->loadSettingsData();
        $this->fillHomeForm();
        $tab = (string) request()->query('tab', '');
        if (in_array($tab, self::TABS, true)) {
            $this->activeTab = $tab;
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, self::TABS, true)) {
            $this->activeTab = $tab;
        }
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('home');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('home')
            ->components([
                TextInput::make('home_welcome_title')
                    ->label(__('settings.home_welcome_title'))
                    ->placeholder(__('settings.home_welcome_title_placeholder'))
                    ->helperText(__('settings.home_welcome_title_help'))
                    ->maxLength(255),
                CmsRichEditor::make('home_welcome_text')
                    ->label(__('settings.home_welcome_text'))
                    ->helperText(__('settings.home_welcome_text_help')),
                Toggle::make('home_show_cta')
                    ->label(__('settings.home_show_cta'))
                    ->helperText(__('settings.home_show_cta_help'))
                    ->default(true),
                TextInput::make('home_cta_primary_label')
                    ->label(__('settings.home_cta_primary_label'))
                    ->placeholder(__('nav.apply'))
                    ->maxLength(100),
                TextInput::make('home_cta_primary_url')
                    ->label(__('settings.home_cta_primary_url'))
                    ->placeholder('/apply')
                    ->helperText(__('settings.home_cta_url_help'))
                    ->maxLength(500),
                TextInput::make('home_cta_secondary_label')
                    ->label(__('settings.home_cta_secondary_label'))
                    ->placeholder(__('nav.news'))
                    ->maxLength(100),
                TextInput::make('home_cta_secondary_url')
                    ->label(__('settings.home_cta_secondary_url'))
                    ->placeholder('/news')
                    ->helperText(__('settings.home_cta_url_help'))
                    ->maxLength(500),
            ]);
    }

    public function save(): void
    {
        $imageKb = UploadLimits::imageKb();

        $this->validate([
            'logoUpload' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:'.$imageKb],
            'bannerUpload' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:'.$imageKb],
            'data.site_name' => ['nullable', 'string', 'max:255'],
            'data.contact_email' => ['nullable', 'email', 'max:255'],
            'data.application_notify_email' => ['nullable', 'email', 'max:255'],
            'data.slider_duration_seconds' => ['nullable', 'integer', 'min:2', 'max:30'],
            'data.upload_max_image_mb' => ['nullable', 'integer', 'min:'.UploadLimits::MIN_MB, 'max:'.UploadLimits::MAX_MB],
            'data.upload_max_file_mb' => ['nullable', 'integer', 'min:'.UploadLimits::MIN_MB, 'max:'.UploadLimits::MAX_MB],
        ], [
            'logoUpload.max' => UploadLimits::imageMaxMessage(__('settings.logo')),
            'bannerUpload.max' => UploadLimits::imageMaxMessage(__('settings.banner')),
        ]);

        $formState = $this->form->getState();
        foreach ([
            'home_welcome_title',
            'home_welcome_text',
            'home_show_cta',
            'home_cta_primary_label',
            'home_cta_primary_url',
            'home_cta_secondary_label',
            'home_cta_secondary_url',
        ] as $homeKey) {
            if (array_key_exists($homeKey, $formState)) {
                $this->data[$homeKey] = $formState[$homeKey];
            }
        }

        if ($this->logoRemove) {
            SiteMedia::deleteStoredPath(setting('site_logo'));
            set_setting('site_logo', '');
            $this->logoRemove = false;
        }

        if ($this->logoUpload instanceof TemporaryUploadedFile) {
            $old = setting('site_logo');
            $path = $this->logoUpload->store('site', 'public');
            set_setting('site_logo', $path);
            if ($old && $old !== $path) {
                SiteMedia::deleteStoredPath($old);
            }
            $this->logoUpload = null;
        }

        if ($this->bannerRemove) {
            SiteMedia::deleteStoredPath(setting('site_banner'));
            set_setting('site_banner', '');
            $this->bannerRemove = false;
        }

        if ($this->bannerUpload instanceof TemporaryUploadedFile) {
            $old = setting('site_banner');
            $path = $this->bannerUpload->store('site', 'public');
            set_setting('site_banner', $path);
            if ($old && $old !== $path) {
                SiteMedia::deleteStoredPath($old);
            }
            $this->bannerUpload = null;
        }

        $boolKeys = [
            'site_banner_enabled',
            'slider_show_arrows',
            'slider_show_dots',
            'footer_credit_enabled',
            'music_dock_enabled',
            'nav_hardcoded_fallback',
            'cookie_banner_enabled',
            'maintenance_enabled',
            'auth_registration_enabled',
            'applications_enabled',
            'home_show_cta',
        ];

        $stringKeys = [
            'site_name',
            'app_locale',
            'app_timezone',
            'date_format',
            'time_format',
            'site_logo_height',
            'site_banner_link',
            'site_banner_alt',
            'contact_address',
            'contact_email',
            'contact_phone',
            'contact_imprint_email',
            'social_discord',
            'social_facebook',
            'social_twitter',
            'social_youtube',
            'social_twitch',
            'social_instagram',
            'social_tiktok',
            'social_steam',
            'donation_url',
            'discord_invite_url',
            'discord_webhook_url',
            'application_notify_email',
            'seo_default_title',
            'seo_default_description',
            'seo_og_image',
            'seo_keywords',
            'seo_og_locale',
            'seo_robots',
            'maintenance_message',
            'slider_duration_seconds',
            'footer_tagline',
            'footer_copyright',
            'footer_credit',
            'footer_pages',
            'music_dock_title',
            'music_dock_subtitle',
            'music_dock_stream_url',
            'cookie_banner_text',
            'cookie_banner_button',
            'cookie_banner_link_label',
            'home_welcome_title',
            'home_welcome_text',
            'home_cta_primary_label',
            'home_cta_primary_url',
            'home_cta_secondary_label',
            'home_cta_secondary_url',
        ];

        foreach ($boolKeys as $key) {
            set_setting($key, ! empty($this->data[$key]) ? '1' : '0');
        }

        foreach ($stringKeys as $key) {
            $value = $this->data[$key] ?? '';
            if ($key === 'slider_duration_seconds') {
                $value = (string) max(2, min(30, (int) $value));
            }
            if ($key === 'app_locale') {
                $value = in_array($value, ['de', 'en'], true) ? $value : 'de';
            }
            if ($key === 'app_timezone') {
                $value = trim((string) $value);
                try {
                    if ($value === '') {
                        $value = 'Europe/Berlin';
                    } else {
                        new \DateTimeZone($value);
                    }
                } catch (\Throwable) {
                    $value = 'Europe/Berlin';
                }
            }
            if ($key === 'date_format') {
                $value = trim((string) $value) !== '' ? trim((string) $value) : 'd.m.Y';
            }
            if ($key === 'time_format') {
                $value = trim((string) $value) !== '' ? trim((string) $value) : 'H:i';
            }
            if ($key === 'site_logo_height') {
                $value = trim((string) $value);
                if ($value !== '' && ! preg_match('/^(\d+(?:\.\d+)?)\s*(%|px|rem|em)?$/iu', $value)) {
                    $value = '';
                }
            }
            if ($key === 'seo_robots') {
                $allowed = ['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'];
                $value = in_array($value, $allowed, true) ? $value : 'index,follow';
            }
            if ($key === 'seo_default_title') {
                $value = trim((string) $value);
                $siteName = trim((string) ($this->data['site_name'] ?? '')) ?: site_name();
                if ($value === '' || $value === (string) config('clan.name', '')) {
                    $value = $siteName;
                }
            }
            set_setting($key, (string) $value);
        }

        $invite = trim((string) ($this->data['discord_invite_url'] ?? ''));
        $socialDiscord = trim((string) ($this->data['social_discord'] ?? ''));
        // Ein Discord-Link: Kontakt & Social ist die Eingabe; Invite spiegelt denselben Wert.
        $discordUrl = $socialDiscord !== '' ? $socialDiscord : $invite;
        set_setting('social_discord', $discordUrl);
        set_setting('discord_invite_url', $discordUrl);
        $this->data['social_discord'] = $discordUrl;
        $this->data['discord_invite_url'] = $discordUrl;

        if (isset($this->data['upload_max_image_mb'])) {
            set_setting('upload_max_image_mb', (string) UploadLimits::clamp((int) $this->data['upload_max_image_mb']));
        }
        if (isset($this->data['upload_max_file_mb'])) {
            set_setting('upload_max_file_mb', (string) UploadLimits::clamp((int) $this->data['upload_max_file_mb']));
        }
        UploadLimits::syncRuntimeLimits();

        $conflict = SiteMedia::enforceBannerSliderExclusivity();

        $maintenanceOn = ! empty($this->data['maintenance_enabled']);

        $this->data = $this->loadSettingsData();
        $this->fillHomeForm();

        $notification = Notification::make()
            ->title(__('settings.saved'));

        $bodyParts = [];
        if ($conflict['resolved'] ?? false) {
            $bodyParts[] = (string) ($conflict['message'] ?? '');
        }
        if ($maintenanceOn) {
            $bodyParts[] = __('settings.maintenance_saved_warn');
            $notification->warning();
        } else {
            $notification->success();
        }
        if ($bodyParts !== []) {
            $notification->body(implode(' ', array_filter($bodyParts)));
        }

        $notification->send();
    }

    protected function fillHomeForm(): void
    {
        $this->form->fill([
            'home_welcome_title' => $this->data['home_welcome_title'] ?? '',
            'home_welcome_text' => $this->data['home_welcome_text'] ?? '',
            'home_show_cta' => (bool) ($this->data['home_show_cta'] ?? true),
            'home_cta_primary_label' => $this->data['home_cta_primary_label'] ?? '',
            'home_cta_primary_url' => $this->data['home_cta_primary_url'] ?? '',
            'home_cta_secondary_label' => $this->data['home_cta_secondary_label'] ?? '',
            'home_cta_secondary_url' => $this->data['home_cta_secondary_url'] ?? '',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadSettingsData(): array
    {
        return [
            'site_name' => site_name(),
            'app_locale' => setting('app_locale', config('app.locale', 'de')),
            'app_timezone' => setting('app_timezone', 'Europe/Berlin'),
            'date_format' => setting('date_format', 'd.m.Y'),
            'time_format' => setting('time_format', 'H:i'),
            'site_logo_height' => setting('site_logo_height', ''),
            'site_banner_enabled' => (bool) filter_var(setting('site_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'site_banner_link' => setting('site_banner_link', ''),
            'site_banner_alt' => setting('site_banner_alt', ''),
            'contact_address' => setting('contact_address', ''),
            'contact_email' => setting('contact_email', ''),
            'contact_phone' => setting('contact_phone', ''),
            'contact_imprint_email' => setting('contact_imprint_email', ''),
            'social_discord' => setting('social_discord', config('clan.discord_invite_url', '')),
            'social_facebook' => setting('social_facebook', ''),
            'social_twitter' => setting('social_twitter', ''),
            'social_youtube' => setting('social_youtube', ''),
            'social_twitch' => setting('social_twitch', ''),
            'social_instagram' => setting('social_instagram', ''),
            'social_tiktok' => setting('social_tiktok', ''),
            'social_steam' => setting('social_steam', ''),
            'donation_url' => setting('donation_url', config('clan.donation_url', '')),
            'discord_invite_url' => setting('discord_invite_url', setting('social_discord', config('clan.discord_invite_url', ''))),
            'discord_webhook_url' => setting('discord_webhook_url', config('clan.discord_webhook_url', '')),
            'application_notify_email' => setting('application_notify_email', config('clan.application_notify_email', '')),
            'seo_default_title' => setting('seo_default_title', site_name()),
            'seo_default_description' => setting('seo_default_description', ''),
            'seo_og_image' => setting('seo_og_image', ''),
            'seo_keywords' => setting('seo_keywords', ''),
            'seo_og_locale' => setting('seo_og_locale', 'de_DE'),
            'seo_robots' => setting('seo_robots', 'index,follow'),
            'maintenance_enabled' => (bool) filter_var(setting('maintenance_enabled', '0'), FILTER_VALIDATE_BOOLEAN),
            'maintenance_message' => setting('maintenance_message', ''),
            'auth_registration_enabled' => (bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'applications_enabled' => (bool) filter_var(setting('applications_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'slider_duration_seconds' => (int) max(2, min(30, (int) setting('slider_duration_seconds', 5))),
            'slider_show_arrows' => (bool) filter_var(setting('slider_show_arrows', '1'), FILTER_VALIDATE_BOOLEAN),
            'slider_show_dots' => (bool) filter_var(setting('slider_show_dots', '1'), FILTER_VALIDATE_BOOLEAN),
            'footer_tagline' => setting('footer_tagline', ''),
            'footer_copyright' => setting('footer_copyright', ''),
            'footer_credit' => setting('footer_credit', ''),
            'footer_credit_enabled' => (bool) filter_var(setting('footer_credit_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'footer_pages' => SiteContent::footerPagesRawDefault(),
            'music_dock_enabled' => (bool) filter_var(setting('music_dock_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'music_dock_title' => setting('music_dock_title', ''),
            'music_dock_subtitle' => setting('music_dock_subtitle', ''),
            'music_dock_stream_url' => setting('music_dock_stream_url', ''),
            'nav_hardcoded_fallback' => (bool) filter_var(setting('nav_hardcoded_fallback', '1'), FILTER_VALIDATE_BOOLEAN),
            'cookie_banner_enabled' => (bool) filter_var(setting('cookie_banner_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'cookie_banner_text' => setting('cookie_banner_text', ''),
            'cookie_banner_button' => setting('cookie_banner_button', ''),
            'cookie_banner_link_label' => setting('cookie_banner_link_label', ''),
            'upload_max_image_mb' => UploadLimits::imageMb(),
            'upload_max_file_mb' => UploadLimits::fileMb(),
            'home_welcome_title' => setting('home_welcome_title', ''),
            'home_welcome_text' => setting('home_welcome_text', ''),
            'home_show_cta' => (bool) filter_var(setting('home_show_cta', '1'), FILTER_VALIDATE_BOOLEAN),
            'home_cta_primary_label' => setting('home_cta_primary_label', ''),
            'home_cta_primary_url' => setting('home_cta_primary_url', ''),
            'home_cta_secondary_label' => setting('home_cta_secondary_label', ''),
            'home_cta_secondary_url' => setting('home_cta_secondary_url', ''),
        ];
    }

    /**
     * @return list<string>
     */
    public function timezoneOptions(): array
    {
        return [
            'Europe/Berlin',
            'Europe/Vienna',
            'Europe/Zurich',
            'Europe/Amsterdam',
            'Europe/London',
            'Europe/Paris',
            'Europe/Madrid',
            'Europe/Rome',
            'Europe/Warsaw',
            'Europe/Moscow',
            'UTC',
            'America/New_York',
            'America/Chicago',
            'America/Los_Angeles',
            'Asia/Tokyo',
            'Asia/Shanghai',
            'Australia/Sydney',
        ];
    }

    public function getViewData(): array
    {
        $logo = Setting::where('key', 'site_logo')->first()?->value ?: config('clan.logo');
        $banner = Setting::where('key', 'site_banner')->first()?->value ?: config('clan.banner');

        return [
            'siteLogo' => $logo,
            'siteBanner' => $banner,
            'phpUploadMb' => UploadLimits::phpUploadMb(),
            'activeSliderCount' => SiteMedia::activeSliderCount(),
            'bannerSliderConflict' => SiteMedia::bannerAndSliderConflict(),
            'modulesUrl' => ModulesPage::getUrl(),
            'themesUrl' => ThemesPage::getUrl(),
            'timezoneOptions' => $this->timezoneOptions(),
            'kingshotCalendarUrl' => url('/admin/kingshot-calendar/settings'),
            'kingshotChatUrl' => url('/admin/kingshot-chat/settings'),
            'kingshotCalendarEnabled' => system_module_enabled('kingshot_calendar'),
            'kingshotChatEnabled' => system_module_enabled('kingshot_chat'),
            'tabs' => [
                'general' => __('settings.general'),
                'media' => __('settings.media'),
                'home' => __('settings.home'),
                'slider' => __('settings.slider'),
                'contact' => __('settings.contact_social'),
                'footer' => __('settings.footer'),
                'integrations' => __('settings.integrations'),
                'cookies' => __('settings.cookies'),
                'seo' => __('settings.seo'),
                'access' => __('settings.access'),
                'uploads' => __('settings.uploads'),
            ],
        ];
    }
}

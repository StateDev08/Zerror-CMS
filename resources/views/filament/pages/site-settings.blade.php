<x-filament-panels::page>
    <div class="zc-page zc-settings">
        @if ($errors->any())
            <div class="zc-alert zc-alert--err">
                <ul style="margin:0;padding-left:1.1rem">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="zc-help">
            <p>{{ __('settings.page_intro') }}</p>
            <p class="zc-help-tip">{{ __('settings.page_intro_tip') }}</p>
            @include('filament.partials.zc-context-links')
        </div>

        <div class="zc-tabs zc-tabs--sticky" role="tablist" aria-label="{{ __('settings.title_tabs') }}">
            @foreach ($tabs as $tabKey => $tabLabel)
                <button
                    type="button"
                    role="tab"
                    wire:click="setTab('{{ $tabKey }}')"
                    class="zc-tab {{ $activeTab === $tabKey ? 'is-active' : '' }}"
                    aria-selected="{{ $activeTab === $tabKey ? 'true' : 'false' }}"
                >{{ $tabLabel }}</button>
            @endforeach
        </div>

        <form wire:submit="save" class="zc-settings__form">
            {{-- Allgemein --}}
            <div class="zc-panel" @if($activeTab !== 'general') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.site_name') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.site_name_help') }}</p>
                    <div class="zc-field">
                        <label for="site_name">{{ __('settings.site_name') }}</label>
                        <input id="site_name" class="zc-input" type="text" wire:model="data.site_name">
                    </div>
                </section>

                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.locale_section') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.locale_section_help') }}</p>
                    <div class="zc-fields--2">
                        <div class="zc-field">
                            <label for="app_locale">{{ __('settings.app_locale') }}</label>
                            <select id="app_locale" class="zc-input" wire:model="data.app_locale">
                                <option value="de">Deutsch</option>
                                <option value="en">English</option>
                            </select>
                            <span class="zc-field-help">{{ __('settings.app_locale_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="app_timezone">{{ __('settings.app_timezone') }}</label>
                            <select id="app_timezone" class="zc-input" wire:model="data.app_timezone">
                                @foreach($timezoneOptions as $tz)
                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                @endforeach
                            </select>
                            <span class="zc-field-help">{{ __('settings.app_timezone_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="date_format">{{ __('settings.date_format') }}</label>
                            <input id="date_format" class="zc-input" type="text" wire:model="data.date_format" placeholder="d.m.Y">
                            <span class="zc-field-help">{{ __('settings.date_format_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="time_format">{{ __('settings.time_format') }}</label>
                            <input id="time_format" class="zc-input" type="text" wire:model="data.time_format" placeholder="H:i">
                            <span class="zc-field-help">{{ __('settings.time_format_help') }}</span>
                        </div>
                    </div>
                </section>

                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.theme_extras') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.theme_extras_description') }}</p>
                    <p class="zc-field-help">{{ __('settings.theme_extras_link_hint') }} <a href="{{ $themesUrl }}" class="zc-inline-link">{{ __('settings.themes_link') }}</a></p>
                    <div class="zc-fields">
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.music_dock_enabled">
                            <span>
                                <strong>{{ __('settings.music_dock_enabled') }}</strong>
                                <span>{{ __('settings.music_dock_enabled_help') }}</span>
                            </span>
                        </label>
                        <div class="zc-fields--2">
                            <div class="zc-field">
                                <label for="music_dock_title">{{ __('settings.music_dock_title') }}</label>
                                <input id="music_dock_title" class="zc-input" type="text" wire:model="data.music_dock_title" placeholder="{{ __('settings.music_dock_title_default') }}">
                            </div>
                            <div class="zc-field">
                                <label for="music_dock_subtitle">{{ __('settings.music_dock_subtitle') }}</label>
                                <input id="music_dock_subtitle" class="zc-input" type="text" wire:model="data.music_dock_subtitle" placeholder="{{ __('settings.music_dock_subtitle_default') }}">
                            </div>
                        </div>
                        <div class="zc-field">
                            <label for="music_dock_stream_url">{{ __('settings.music_dock_stream_url') }}</label>
                            <input id="music_dock_stream_url" class="zc-input" type="url" wire:model="data.music_dock_stream_url" placeholder="{{ __('settings.placeholder_url') }}">
                        </div>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.nav_hardcoded_fallback">
                            <span>
                                <strong>{{ __('settings.nav_hardcoded_fallback') }}</strong>
                                <span>{{ __('settings.nav_hardcoded_fallback_help') }}</span>
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            {{-- Medien --}}
            <div class="zc-panel" @if($activeTab !== 'media') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.logo') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.logo_description') }}</p>
                    @if($siteLogo ?? null)
                        <div class="zc-preview">
                            <span class="zc-field-help">{{ __('settings.logo_current') }}</span>
                            <img class="zc-preview--logo" src="{{ storage_asset($siteLogo) }}" alt="Logo">
                        </div>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="logoRemove">
                            <span>
                                <strong>{{ __('settings.logo_remove') }}</strong>
                                <span>{{ __('settings.logo_remove_help') }}</span>
                            </span>
                        </label>
                    @endif
                    <div class="zc-field">
                        <label for="logoUpload">{{ __('settings.logo_upload') }}</label>
                        <input id="logoUpload" class="zc-file" type="file" wire:model="logoUpload" accept="image/jpeg,image/png,image/gif,image/webp">
                        <span class="zc-field-help">{{ __('settings.upload_limit_hint', ['mb' => $data['upload_max_image_mb'] ?? 10]) }}</span>
                        <div wire:loading wire:target="logoUpload" class="zc-field-help">{{ __('settings.uploading') }}</div>
                    </div>
                    <div class="zc-field">
                        <label for="site_logo_height">{{ __('settings.site_logo_height') }}</label>
                        <input id="site_logo_height" class="zc-input" type="text" wire:model="data.site_logo_height" placeholder="80px / 200% / 3rem">
                        <span class="zc-field-help">{{ __('settings.site_logo_height_help') }}</span>
                    </div>
                </section>

                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.banner') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.banner_description') }}</p>
                    <div class="zc-alert zc-alert--info">{{ __('settings.banner_slider_exclusive_info') }}</div>
                    @if(!empty($bannerSliderConflict))
                        <div class="zc-alert zc-alert--err">{{ __('settings.banner_slider_conflict_now') }}</div>
                    @endif

                    <label class="zc-check">
                        <input type="checkbox" wire:model="data.site_banner_enabled">
                        <span>
                            <strong>{{ __('settings.banner_enabled') }}</strong>
                            <span>{{ __('settings.banner_enabled_help') }}</span>
                        </span>
                    </label>

                    @if($siteBanner ?? null)
                        <div class="zc-preview">
                            <span class="zc-field-help">{{ __('settings.banner_current') }}</span>
                            <img class="zc-preview--banner" src="{{ storage_asset($siteBanner) }}" alt="">
                        </div>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="bannerRemove">
                            <span>
                                <strong>{{ __('settings.banner_remove') }}</strong>
                                <span>{{ __('settings.banner_remove_help') }}</span>
                            </span>
                        </label>
                    @endif

                    <div class="zc-fields--2">
                        <div class="zc-field">
                            <label for="site_banner_link">{{ __('settings.banner_link') }}</label>
                            <input id="site_banner_link" class="zc-input" type="url" wire:model="data.site_banner_link" placeholder="{{ __('settings.placeholder_url') }}">
                        </div>
                        <div class="zc-field">
                            <label for="site_banner_alt">{{ __('settings.banner_alt') }}</label>
                            <input id="site_banner_alt" class="zc-input" type="text" wire:model="data.site_banner_alt">
                        </div>
                        <div class="zc-field">
                            <label for="bannerUpload">{{ __('settings.banner_upload') }}</label>
                            <input id="bannerUpload" class="zc-file" type="file" wire:model="bannerUpload" accept="image/jpeg,image/png,image/gif,image/webp">
                            <span class="zc-field-help">{{ __('settings.upload_limit_hint', ['mb' => $data['upload_max_image_mb'] ?? 10]) }}</span>
                            <div wire:loading wire:target="bannerUpload" class="zc-field-help">{{ __('settings.uploading') }}</div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Startseite --}}
            <div class="zc-panel" @if($activeTab !== 'home') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.home') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.home_description') }}</p>
                    <div class="zc-alert zc-alert--info">{{ __('settings.home_widgets_hint') }}</div>
                    {{ $this->form }}
                </section>
            </div>

            {{-- Slider --}}
            <div class="zc-panel" @if($activeTab !== 'slider') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.slider') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.slider_description') }}</p>
                    <div class="zc-alert zc-alert--info">{{ __('settings.banner_slider_exclusive_info') }}</div>
                    @if(!empty($activeSliderCount))
                        <p class="zc-field-help">{{ __('settings.slider_active_count', ['count' => $activeSliderCount]) }}</p>
                    @endif
                    <div class="zc-field">
                        <label for="slider_duration_seconds">{{ __('settings.slider_duration_seconds') }}</label>
                        <input id="slider_duration_seconds" class="zc-input zc-narrow" type="number" wire:model="data.slider_duration_seconds" min="2" max="30">
                    </div>
                    <div class="zc-fields">
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.slider_show_arrows">
                            <span>
                                <strong>{{ __('settings.slider_show_arrows') }}</strong>
                                <span>{{ __('settings.slider_show_arrows_help') }}</span>
                            </span>
                        </label>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.slider_show_dots">
                            <span>
                                <strong>{{ __('settings.slider_show_dots') }}</strong>
                                <span>{{ __('settings.slider_show_dots_help') }}</span>
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            {{-- Kontakt & Social --}}
            <div class="zc-panel" @if($activeTab !== 'contact') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.contact') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.contact_description') }}</p>
                    <div class="zc-fields">
                        <div class="zc-field">
                            <label for="contact_address">{{ __('settings.contact_address') }}</label>
                            <textarea id="contact_address" class="zc-textarea" rows="3" wire:model="data.contact_address"></textarea>
                        </div>
                        <div class="zc-fields--2">
                            <div class="zc-field">
                                <label for="contact_email">{{ __('settings.contact_email') }}</label>
                                <input id="contact_email" class="zc-input" type="email" wire:model="data.contact_email">
                            </div>
                            <div class="zc-field">
                                <label for="contact_phone">{{ __('settings.contact_phone') }}</label>
                                <input id="contact_phone" class="zc-input" type="text" wire:model="data.contact_phone">
                            </div>
                            <div class="zc-field">
                                <label for="contact_imprint_email">{{ __('settings.contact_imprint_email') }}</label>
                                <input id="contact_imprint_email" class="zc-input" type="email" wire:model="data.contact_imprint_email">
                                <span class="zc-field-help">{{ __('settings.contact_imprint_email_help') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.social') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.social_description') }}</p>
                    <div class="zc-fields--2">
                        <div class="zc-field">
                            <label for="social_discord">{{ __('settings.social_discord') }}</label>
                            <input id="social_discord" class="zc-input" type="url" wire:model="data.social_discord" placeholder="{{ __('settings.placeholder_discord_url') }}">
                            <span class="zc-field-help">{{ __('settings.social_discord_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="social_facebook">{{ __('settings.social_facebook') }}</label>
                            <input id="social_facebook" class="zc-input" type="url" wire:model="data.social_facebook">
                        </div>
                        <div class="zc-field">
                            <label for="social_twitter">{{ __('settings.social_twitter') }}</label>
                            <input id="social_twitter" class="zc-input" type="url" wire:model="data.social_twitter">
                        </div>
                        <div class="zc-field">
                            <label for="social_youtube">{{ __('settings.social_youtube') }}</label>
                            <input id="social_youtube" class="zc-input" type="url" wire:model="data.social_youtube">
                        </div>
                        <div class="zc-field">
                            <label for="social_twitch">{{ __('settings.social_twitch') }}</label>
                            <input id="social_twitch" class="zc-input" type="url" wire:model="data.social_twitch">
                        </div>
                        <div class="zc-field">
                            <label for="social_instagram">{{ __('settings.social_instagram') }}</label>
                            <input id="social_instagram" class="zc-input" type="url" wire:model="data.social_instagram">
                        </div>
                        <div class="zc-field">
                            <label for="social_tiktok">{{ __('settings.social_tiktok') }}</label>
                            <input id="social_tiktok" class="zc-input" type="url" wire:model="data.social_tiktok">
                        </div>
                        <div class="zc-field">
                            <label for="social_steam">{{ __('settings.social_steam') }}</label>
                            <input id="social_steam" class="zc-input" type="url" wire:model="data.social_steam">
                        </div>
                        <div class="zc-field">
                            <label for="donation_url">{{ __('settings.donation_url') }}</label>
                            <input id="donation_url" class="zc-input" type="url" wire:model="data.donation_url" placeholder="{{ __('settings.placeholder_url') }}">
                            <span class="zc-field-help">{{ __('settings.donation_url_help') }}</span>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Footer --}}
            <div class="zc-panel" @if($activeTab !== 'footer') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.footer') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.footer_description') }}</p>
                    <div class="zc-fields">
                        <div class="zc-field">
                            <label for="footer_tagline">{{ __('settings.footer_tagline') }}</label>
                            <input id="footer_tagline" class="zc-input" type="text" wire:model="data.footer_tagline">
                            <span class="zc-field-help">{{ __('settings.footer_tagline_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="footer_copyright">{{ __('settings.footer_copyright') }}</label>
                            <input id="footer_copyright" class="zc-input" type="text" wire:model="data.footer_copyright" placeholder="{{ __('footer.copyright') }}">
                            <span class="zc-field-help">{{ __('settings.footer_copyright_help') }}</span>
                        </div>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.footer_credit_enabled">
                            <span>
                                <strong>{{ __('settings.footer_credit_enabled') }}</strong>
                                <span>{{ __('settings.footer_credit_enabled_help') }}</span>
                            </span>
                        </label>
                        <div class="zc-field">
                            <label for="footer_credit">{{ __('settings.footer_credit') }}</label>
                            <input id="footer_credit" class="zc-input" type="text" wire:model="data.footer_credit" placeholder="{{ __('footer.developed_by') }}">
                        </div>
                        <div class="zc-field">
                            <label for="footer_pages">{{ __('settings.footer_pages') }}</label>
                            <textarea id="footer_pages" class="zc-textarea" rows="5" wire:model="data.footer_pages" placeholder="Impressum|impressum"></textarea>
                            <span class="zc-field-help">{{ __('settings.footer_pages_help') }}</span>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Integrationen --}}
            <div class="zc-panel" @if($activeTab !== 'integrations') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.integrations') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.integrations_description') }}</p>
                    <div class="zc-fields">
                        <div class="zc-alert zc-alert--info">
                            {{ __('settings.discord_link_ssot_hint') }}
                        </div>
                        <div class="zc-field">
                            <label for="discord_webhook_url">{{ __('settings.discord_webhook_url') }}</label>
                            <input id="discord_webhook_url" class="zc-input" type="url" wire:model="data.discord_webhook_url" placeholder="https://discord.com/api/webhooks/…">
                            <span class="zc-field-help">{{ __('settings.discord_webhook_url_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="application_notify_email">{{ __('settings.application_notify_email') }}</label>
                            <input id="application_notify_email" class="zc-input" type="email" wire:model="data.application_notify_email">
                            <span class="zc-field-help">{{ __('settings.application_notify_email_help') }}</span>
                        </div>
                    </div>
                </section>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.kingshot_integrations') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.kingshot_integrations_help') }}</p>
                    <div class="zc-fields">
                        <p class="zc-field-help">{{ __('settings.kingshot_timezone_hint') }}</p>
                        @if($kingshotCalendarEnabled ?? false)
                            <p><a href="{{ $kingshotCalendarUrl }}" class="zc-inline-link" target="_blank" rel="noopener">{{ __('settings.kingshot_calendar_link') }}</a></p>
                        @endif
                        @if($kingshotChatEnabled ?? false)
                            <p><a href="{{ $kingshotChatUrl }}" class="zc-inline-link" target="_blank" rel="noopener">{{ __('settings.kingshot_chat_link') }}</a></p>
                        @endif
                        @if(! ($kingshotCalendarEnabled ?? false) && ! ($kingshotChatEnabled ?? false))
                            <p class="zc-field-help">{{ __('settings.kingshot_modules_disabled') }}</p>
                        @endif
                    </div>
                </section>
            </div>

            {{-- Cookies --}}
            <div class="zc-panel" @if($activeTab !== 'cookies') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.cookies') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.cookies_description') }}</p>
                    <div class="zc-fields">
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.cookie_banner_enabled">
                            <span>
                                <strong>{{ __('settings.cookie_banner_enabled') }}</strong>
                                <span>{{ __('settings.cookie_banner_enabled_help') }}</span>
                            </span>
                        </label>
                        <div class="zc-field">
                            <label for="cookie_banner_text">{{ __('settings.cookie_banner_text') }}</label>
                            <textarea id="cookie_banner_text" class="zc-textarea" rows="3" wire:model="data.cookie_banner_text" placeholder="{{ __('cookies.banner_text') }}"></textarea>
                        </div>
                        <div class="zc-fields--2">
                            <div class="zc-field">
                                <label for="cookie_banner_button">{{ __('settings.cookie_banner_button') }}</label>
                                <input id="cookie_banner_button" class="zc-input" type="text" wire:model="data.cookie_banner_button" placeholder="{{ __('cookies.accept') }}">
                            </div>
                            <div class="zc-field">
                                <label for="cookie_banner_link_label">{{ __('settings.cookie_banner_link_label') }}</label>
                                <input id="cookie_banner_link_label" class="zc-input" type="text" wire:model="data.cookie_banner_link_label" placeholder="{{ __('nav.cookies') }}">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- SEO --}}
            <div class="zc-panel" @if($activeTab !== 'seo') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.seo') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.seo_description') }}</p>
                    <div class="zc-fields">
                        <div class="zc-field">
                            <label for="seo_default_title">{{ __('settings.seo_default_title') }}</label>
                            <input id="seo_default_title" class="zc-input" type="text" wire:model="data.seo_default_title">
                            <span class="zc-field-help">{{ __('settings.seo_default_title_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="seo_default_description">{{ __('settings.seo_default_description') }}</label>
                            <textarea id="seo_default_description" class="zc-textarea" rows="3" wire:model="data.seo_default_description"></textarea>
                        </div>
                        <div class="zc-field">
                            <label for="seo_keywords">{{ __('settings.seo_keywords') }}</label>
                            <textarea id="seo_keywords" class="zc-textarea" rows="2" wire:model="data.seo_keywords" placeholder="clan, kingshot, …"></textarea>
                            <span class="zc-field-help">{{ __('settings.seo_keywords_help') }}</span>
                        </div>
                        <div class="zc-fields--2">
                            <div class="zc-field">
                                <label for="seo_og_locale">{{ __('settings.seo_og_locale') }}</label>
                                <input id="seo_og_locale" class="zc-input" type="text" wire:model="data.seo_og_locale" placeholder="de_DE">
                            </div>
                            <div class="zc-field">
                                <label for="seo_robots">{{ __('settings.seo_robots') }}</label>
                                <select id="seo_robots" class="zc-input" wire:model="data.seo_robots">
                                    <option value="index,follow">index,follow</option>
                                    <option value="noindex,follow">noindex,follow</option>
                                    <option value="index,nofollow">index,nofollow</option>
                                    <option value="noindex,nofollow">noindex,nofollow</option>
                                </select>
                            </div>
                        </div>
                        <div class="zc-field">
                            <label for="seo_og_image">{{ __('settings.seo_og_image') }}</label>
                            <input id="seo_og_image" class="zc-input" type="url" wire:model="data.seo_og_image" placeholder="{{ __('settings.placeholder_og_image_url') }}">
                        </div>
                    </div>
                </section>
            </div>

            {{-- Zugang --}}
            <div class="zc-panel" @if($activeTab !== 'access') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.registration') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.registration_description') }}</p>
                    <div class="zc-fields">
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.auth_registration_enabled">
                            <span>
                                <strong>{{ __('settings.auth_registration_enabled') }}</strong>
                                <span>{{ __('settings.auth_registration_enabled_help') }}</span>
                            </span>
                        </label>
                        <label class="zc-check">
                            <input type="checkbox" wire:model="data.applications_enabled">
                            <span>
                                <strong>{{ __('settings.applications_enabled') }}</strong>
                                <span>{{ __('settings.applications_enabled_help') }}</span>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.maintenance') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.maintenance_description') }}</p>
                    <label class="zc-check">
                        <input type="checkbox" wire:model.live="data.maintenance_enabled">
                        <span>
                            <strong>{{ __('settings.maintenance_enabled') }}</strong>
                            <span>{{ __('settings.maintenance_enabled_help') }}</span>
                        </span>
                    </label>
                    @if(! empty($data['maintenance_enabled']))
                        <div class="zc-note zc-note--warn" style="margin-top:0.85rem">
                            {{ __('settings.maintenance_warn') }}
                        </div>
                    @endif
                    <div class="zc-field" style="margin-top:1rem">
                        <label for="maintenance_message">{{ __('settings.maintenance_message') }}</label>
                        <textarea id="maintenance_message" class="zc-textarea" rows="3" wire:model="data.maintenance_message" placeholder="{{ __('maintenance.message') }}"></textarea>
                        <span class="zc-field-help">{{ __('settings.maintenance_message_help') }}</span>
                    </div>
                </section>
            </div>

            {{-- Uploads --}}
            <div class="zc-panel" @if($activeTab !== 'uploads') hidden @endif>
                <section class="zc-section">
                    <h3 class="zc-section__title">{{ __('settings.uploads') }}</h3>
                    <p class="zc-section__desc">{{ __('settings.uploads_description') }}</p>
                    <div class="zc-fields--2">
                        <div class="zc-field">
                            <label for="upload_max_image_mb">{{ __('settings.upload_max_image_mb') }}</label>
                            <input id="upload_max_image_mb" class="zc-input zc-narrow" type="number" wire:model="data.upload_max_image_mb" min="1" max="512" step="1">
                            <span class="zc-field-help">{{ __('settings.upload_max_image_help') }}</span>
                        </div>
                        <div class="zc-field">
                            <label for="upload_max_file_mb">{{ __('settings.upload_max_file_mb') }}</label>
                            <input id="upload_max_file_mb" class="zc-input zc-narrow" type="number" wire:model="data.upload_max_file_mb" min="1" max="512" step="1">
                            <span class="zc-field-help">{{ __('settings.upload_max_file_help') }}</span>
                        </div>
                    </div>
                    @if(!empty($phpUploadMb))
                        <p class="zc-field-help" style="margin-top:0.75rem">
                            {{ __('settings.upload_php_limit', ['mb' => $phpUploadMb]) }}
                        </p>
                    @endif
                </section>
            </div>

            <div class="zc-actions zc-actions--sticky">
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('settings.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('settings.saving') }}</span>
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>

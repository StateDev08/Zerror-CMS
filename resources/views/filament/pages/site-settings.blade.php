<x-filament-panels::page>
    @if (session('success'))
        <p class="mb-4 p-3 rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</p>
    @endif

    <div x-data="{ tab: 'general' }" class="space-y-6">
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
            <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.general') }}</button>
            <button type="button" @click="tab = 'contact'" :class="tab === 'contact' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.contact') }}</button>
            <button type="button" @click="tab = 'social'" :class="tab === 'social' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.social') }}</button>
            <button type="button" @click="tab = 'seo'" :class="tab === 'seo' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.seo') }}</button>
            <button type="button" @click="tab = 'maintenance'" :class="tab === 'maintenance' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.maintenance') }}</button>
            <button type="button" @click="tab = 'registration'" :class="tab === 'registration' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.registration') }}</button>
            <button type="button" @click="tab = 'slider'" :class="tab === 'slider' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-3 py-1.5 rounded text-sm font-medium">{{ __('settings.slider') }}</button>
        </div>

        <form action="{{ route('filament.admin.site-media') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div x-show="tab === 'general'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.site_name') }}</x-slot>
                    <x-slot name="description">Wird im Header und als Standard-Titel verwendet.</x-slot>
                    <input type="text" name="site_name" value="{{ old('site_name', $siteName ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                </x-filament::section>
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.logo') }} (Header)</x-slot>
                    <x-slot name="description">Wird im Kopfbereich der Seite angezeigt. Leer = Clanname als Text.</x-slot>
                    @if($siteLogo ?? null)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Aktuelles Logo:</p>
                        <img src="{{ storage_asset($siteLogo) }}" alt="Logo" class="h-16 w-auto object-contain mb-4">
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm">
                    @error('logo') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </x-filament::section>
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.banner') }}</x-slot>
                    <x-slot name="description">{{ __('settings.banner_description') }}</x-slot>
                    <label class="flex items-center gap-2 mb-4">
                        <input type="hidden" name="site_banner_enabled" value="0">
                        <input type="checkbox" name="site_banner_enabled" value="1" {{ (old('site_banner_enabled', $siteBannerEnabled ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <span>{{ __('settings.banner_enabled') }}</span>
                    </label>
                    @if($siteBanner ?? null)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('settings.banner_current') }}</p>
                        <img src="{{ storage_asset($siteBanner) }}" alt="" class="w-full max-h-40 object-cover mb-4">
                        <label class="flex items-center gap-2 mb-4">
                            <input type="checkbox" name="banner_remove" value="1">
                            <span>{{ __('settings.banner_remove') }}</span>
                        </label>
                    @endif
                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.banner_link') }}</label>
                            <input type="url" name="site_banner_link" value="{{ old('site_banner_link', $siteBannerLink ?? '') }}" placeholder="{{ __('settings.placeholder_url') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.banner_alt') }}</label>
                            <input type="text" name="site_banner_alt" value="{{ old('site_banner_alt', $siteBannerAlt ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.banner_height') }}</label>
                            <select name="site_banner_height" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                                <option value="small" {{ ($siteBannerHeight ?? 'medium') === 'small' ? 'selected' : '' }}>{{ __('settings.banner_height_small') }}</option>
                                <option value="medium" {{ ($siteBannerHeight ?? 'medium') === 'medium' ? 'selected' : '' }}>{{ __('settings.banner_height_medium') }}</option>
                                <option value="large" {{ ($siteBannerHeight ?? 'medium') === 'large' ? 'selected' : '' }}>{{ __('settings.banner_height_large') }}</option>
                            </select>
                        </div>
                    </div>
                    <input type="file" name="banner" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm">
                    @error('banner') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </x-filament::section>
            </div>

            <div x-show="tab === 'contact'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.contact') }}</x-slot>
                    <x-slot name="description">Angaben für Impressum und Footer.</x-slot>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.contact_address') }}</label>
                            <textarea name="contact_address" rows="2" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('contact_address', $contactAddress ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.contact_email') }}</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $contactEmail ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.contact_phone') }}</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $contactPhone ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <div x-show="tab === 'social'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.social') }}</x-slot>
                    <x-slot name="description">URLs zu Discord, Facebook, Twitter/X, YouTube (im Footer angezeigt).</x-slot>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Discord</label>
                            <input type="url" name="social_discord" value="{{ old('social_discord', $socialDiscord ?? '') }}" placeholder="{{ __('settings.placeholder_discord_url') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Facebook</label>
                            <input type="url" name="social_facebook" value="{{ old('social_facebook', $socialFacebook ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Twitter / X</label>
                            <input type="url" name="social_twitter" value="{{ old('social_twitter', $socialTwitter ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">YouTube</label>
                            <input type="url" name="social_youtube" value="{{ old('social_youtube', $socialYoutube ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <div x-show="tab === 'seo'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.seo') }}</x-slot>
                    <x-slot name="description">Standard-Meta-Angaben für alle Seiten (falls nicht seitenweise überschrieben).</x-slot>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Meta-Titel</label>
                            <input type="text" name="seo_default_title" value="{{ old('seo_default_title', $seoDefaultTitle ?? '') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Meta-Beschreibung</label>
                            <textarea name="seo_default_description" rows="2" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('seo_default_description', $seoDefaultDescription ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">OG-Image URL</label>
                            <input type="url" name="seo_og_image" value="{{ old('seo_og_image', $seoOgImage ?? '') }}" placeholder="{{ __('settings.placeholder_og_image_url') }}" class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <div x-show="tab === 'maintenance'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.maintenance') }}</x-slot>
                    <x-slot name="description">Bei aktivem Wartungsmodus sehen Besucher eine Wartungsseite. Admins können sich weiterhin anmelden.</x-slot>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="maintenance_enabled" value="0">
                        <input type="checkbox" name="maintenance_enabled" value="1" {{ (old('maintenance_enabled', ($maintenanceEnabled ?? false) ? '1' : '0') === '1') ? 'checked' : '' }}>
                        <span>{{ __('settings.maintenance_enabled') }}</span>
                    </label>
                </x-filament::section>
            </div>

            <div x-show="tab === 'registration'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.registration') }}</x-slot>
                    <x-slot name="description">Registrierung und Bewerbungsformular ein- oder ausblenden.</x-slot>
                    <div class="space-y-4">
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="auth_registration_enabled" value="0">
                            <input type="checkbox" name="auth_registration_enabled" value="1" {{ (old('auth_registration_enabled', ($authRegistrationEnabled ?? true) ? '1' : '0') === '1') ? 'checked' : '' }}>
                            <span>{{ __('settings.auth_registration_enabled') }}</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="applications_enabled" value="0">
                            <input type="checkbox" name="applications_enabled" value="1" {{ (old('applications_enabled', ($applicationsEnabled ?? true) ? '1' : '0') === '1') ? 'checked' : '' }}>
                            <span>{{ __('settings.applications_enabled') }}</span>
                        </label>
                    </div>
                </x-filament::section>
            </div>

            <div x-show="tab === 'slider'" x-cloak class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">{{ __('settings.slider') }}</x-slot>
                    <x-slot name="description">{{ __('settings.slider_description') }}</x-slot>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.slider_duration_seconds') }}</label>
                            <input type="number" name="slider_duration_seconds" value="{{ old('slider_duration_seconds', $sliderDurationSeconds ?? 5) }}" min="2" max="30" class="fi-input block w-full max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('settings.slider_height') }}</label>
                            <select name="slider_height" class="fi-input block w-full max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">
                                <option value="small" {{ ($sliderHeight ?? 'medium') === 'small' ? 'selected' : '' }}>{{ __('settings.slider_height_small') }}</option>
                                <option value="medium" {{ ($sliderHeight ?? 'medium') === 'medium' ? 'selected' : '' }}>{{ __('settings.slider_height_medium') }}</option>
                                <option value="large" {{ ($sliderHeight ?? 'medium') === 'large' ? 'selected' : '' }}>{{ __('settings.slider_height_large') }}</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="slider_show_arrows" value="0">
                            <input type="checkbox" name="slider_show_arrows" value="1" {{ (old('slider_show_arrows', $sliderShowArrows ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}>
                            <span>{{ __('settings.slider_show_arrows') }}</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="slider_show_dots" value="0">
                            <input type="checkbox" name="slider_show_dots" value="1" {{ (old('slider_show_dots', $sliderShowDots ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}>
                            <span>{{ __('settings.slider_show_dots') }}</span>
                        </label>
                    </div>
                </x-filament::section>
            </div>

            <x-filament::button type="submit">
                Speichern
            </x-filament::button>
        </form>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-filament-panels::page>

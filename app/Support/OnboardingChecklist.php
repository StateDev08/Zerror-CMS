<?php

namespace App\Support;

use App\Filament\Pages\ModulesPage;
use App\Filament\Pages\PackagesInstallerPage;
use App\Filament\Pages\SiteSettingsPage;
use App\Filament\Pages\ThemesPage;
use App\Filament\Resources\MenuItemResource;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\WidgetInstanceResource;
use App\Models\MenuItem;
use App\Models\Post;
use Illuminate\Support\Facades\Schema;

/**
 * Erststart-Checkliste für das ACP-Dashboard.
 */
class OnboardingChecklist
{
    public const DISMISS_KEY = 'onboarding_dismissed';

    /**
     * @return list<array{id: string, done: bool, label: string, hint: string, url: ?string}>
     */
    public function steps(): array
    {
        return [
            [
                'id' => 'logo',
                'done' => $this->hasLogo(),
                'label' => __('zerrocms.onboarding.step_logo'),
                'hint' => __('zerrocms.onboarding.hint_logo'),
                'url' => $this->safeUrl(fn () => SiteSettingsPage::getUrl().'?tab=media'),
            ],
            [
                'id' => 'menu',
                'done' => $this->hasMenu(),
                'label' => __('zerrocms.onboarding.step_menu'),
                'hint' => __('zerrocms.onboarding.hint_menu'),
                'url' => $this->safeUrl(fn () => MenuItemResource::getUrl('index')),
            ],
            [
                'id' => 'home',
                'done' => $this->hasHomeContent(),
                'label' => __('zerrocms.onboarding.step_home'),
                'hint' => __('zerrocms.onboarding.hint_home'),
                'url' => $this->safeUrl(fn () => SiteSettingsPage::getUrl().'?tab=home'),
            ],
            [
                'id' => 'discord',
                'done' => $this->hasDiscord(),
                'label' => __('zerrocms.onboarding.step_discord'),
                'hint' => __('zerrocms.onboarding.hint_discord'),
                'url' => $this->safeUrl(fn () => SiteSettingsPage::getUrl().'?tab=contact'),
            ],
            [
                'id' => 'news',
                'done' => $this->hasNewsPost(),
                'label' => __('zerrocms.onboarding.step_news'),
                'hint' => __('zerrocms.onboarding.hint_news'),
                'url' => $this->safeUrl(fn () => PostResource::getUrl('create')),
            ],
        ];
    }

    public function doneCount(): int
    {
        return count(array_filter($this->steps(), fn (array $s) => $s['done']));
    }

    public function totalCount(): int
    {
        return count($this->steps());
    }

    public function isComplete(): bool
    {
        return $this->doneCount() >= $this->totalCount();
    }

    public function isDismissed(): bool
    {
        return filter_var(setting(self::DISMISS_KEY, '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function dismiss(): void
    {
        set_setting(self::DISMISS_KEY, '1');
    }

    public function shouldShow(): bool
    {
        if ($this->isDismissed()) {
            return false;
        }

        return ! $this->isComplete();
    }

    /**
     * Kontext-Links Einstellungen ↔ Themes ↔ Widgets ↔ Module ↔ Installer.
     *
     * @return list<array{label: string, url: string}>
     */
    public function contextLinks(): array
    {
        $candidates = [
            [SiteSettingsPage::class, __('zerrocms.nav.settings'), false],
            [ThemesPage::class, __('zerrocms.nav.themes'), false],
            [WidgetInstanceResource::class, __('zerrocms.nav.widgets'), true],
            [ModulesPage::class, __('zerrocms.nav.modules'), false],
            [PackagesInstallerPage::class, __('zerrocms.nav.packages'), false],
        ];

        $out = [];
        foreach ($candidates as [$class, $label, $isResource]) {
            if (! class_exists($class)) {
                continue;
            }
            try {
                if (method_exists($class, 'canAccess') && ! $class::canAccess()) {
                    continue;
                }
                if ($isResource && method_exists($class, 'canViewAny') && ! $class::canViewAny()) {
                    continue;
                }
                $url = $isResource ? $class::getUrl('index') : $class::getUrl();
                $out[] = ['label' => $label, 'url' => $url];
            } catch (\Throwable) {
                continue;
            }
        }

        return $out;
    }

    private function hasLogo(): bool
    {
        return trim((string) setting('site_logo', '')) !== '';
    }

    private function hasMenu(): bool
    {
        if (! Schema::hasTable('menu_items')) {
            return false;
        }

        return MenuItem::query()->exists();
    }

    private function hasHomeContent(): bool
    {
        $title = trim(strip_tags((string) setting('home_welcome_title', '')));
        $text = trim(strip_tags((string) setting('home_welcome_text', '')));

        return $title !== '' || $text !== '';
    }

    private function hasDiscord(): bool
    {
        $invite = trim((string) setting('discord_invite_url', ''));
        $social = trim((string) setting('social_discord', ''));

        return $invite !== '' || $social !== '';
    }

    private function hasNewsPost(): bool
    {
        if (! Schema::hasTable('posts')) {
            return false;
        }

        return Post::query()->where('type', 'news')->where('published', true)->exists();
    }

    private function safeUrl(callable $resolver): ?string
    {
        try {
            $url = $resolver();

            return is_string($url) && $url !== '' ? $url : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

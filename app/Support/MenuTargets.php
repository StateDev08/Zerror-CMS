<?php

namespace App\Support;

use App\Models\CmsPage;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class MenuTargets
{
    /**
     * Grouped select options for Filament: route names / paths => labels.
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        return [
            __('menu.group_modules') => static::moduleTargets(),
            __('menu.group_pages') => static::pageTargets(),
            __('menu.group_account') => static::accountTargets(),
        ];
    }

    /**
     * Flat map of all known targets (value => label).
     *
     * @return array<string, string>
     */
    public static function flatOptions(): array
    {
        $flat = [];
        foreach (static::groupedOptions() as $group) {
            foreach ($group as $value => $label) {
                $flat[$value] = $label;
            }
        }

        return $flat;
    }

    public static function isKnown(string $link): bool
    {
        $link = trim($link);

        return $link !== '' && array_key_exists($link, static::flatOptions());
    }

    public static function labelFor(string $link): ?string
    {
        return static::flatOptions()[trim($link)] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public static function moduleTargets(): array
    {
        $candidates = [
            'home' => 'nav.home',
            'news.index' => 'nav.news',
            'roster.index' => 'nav.roster',
            'calendar.index' => 'nav.calendar',
            'apply.index' => 'nav.apply',
            'forum.index' => 'nav.forum',
            'wiki.index' => 'nav.wiki',
            'gallery.index' => 'nav.gallery',
            'downloads.index' => 'nav.downloads',
            'partners.index' => 'nav.partners',
            'servers.index' => 'nav.servers',
            'polls.index' => 'nav.polls',
            'newsletter.index' => 'nav.newsletter',
            'marketplace.index' => 'nav.marketplace',
            'jobs.index' => 'nav.jobs',
            'crafting.index' => 'nav.crafting',
            'clan-teams.index' => 'nav.clan_teams',
            'clan-bank.index' => 'nav.clan_bank',
            'clan-treasury.index' => 'nav.clan_treasury',
            'clan-rules.index' => 'nav.clan_rules',
            'clan-leaderboard.index' => 'nav.clan_leaderboard',
            'clan-documents.index' => 'nav.clan_documents',
            'clan-feedback.index' => 'nav.clan_feedback',
            'clan-announcements.index' => 'nav.clan_announcements',
            'clan-achievements.index' => 'nav.clan_achievements',
            'kingshot.guides.index' => 'Kingshot Guides',
        ];

        $out = [];
        foreach ($candidates as $route => $langKey) {
            if ($route === 'home' || Route::has($route)) {
                $out[$route] = __($langKey);
            }
        }

        return $out;
    }

    /**
     * CMS-Seiten (cms_pages) + Legacy Post type=page + Footer-Slugs.
     *
     * @return array<string, string>
     */
    public static function pageTargets(): array
    {
        $out = [];

        foreach (config('clan.footer_pages', []) as $slug => $labelKey) {
            $path = '/page/'.$slug;
            $out[$path] = is_string($labelKey) && str_contains($labelKey, '.')
                ? __($labelKey)
                : (string) $labelKey;
        }

        if (Schema::hasTable('cms_pages') && class_exists(CmsPage::class)) {
            try {
                CmsPage::query()
                    ->orderBy('title')
                    ->get(['title', 'slug', 'published'])
                    ->each(function (CmsPage $page) use (&$out): void {
                        $path = '/page/'.$page->slug;
                        $label = $page->title;
                        if (! $page->published) {
                            $label .= ' ('.__('menu.draft').')';
                        }
                        $out[$path] = $label;
                    });
            } catch (\Throwable) {
                // During early install
            }
        }

        if (class_exists(Post::class)) {
            try {
                Post::query()
                    ->where('type', 'page')
                    ->orderBy('title')
                    ->get(['title', 'slug', 'published'])
                    ->each(function (Post $page) use (&$out): void {
                        $path = '/page/'.$page->slug;
                        if (isset($out[$path])) {
                            return;
                        }
                        $label = $page->title;
                        if (! $page->published) {
                            $label .= ' ('.__('menu.draft').')';
                        }
                        $out[$path] = $label;
                    });
            } catch (\Throwable) {
                // During early install / missing table
            }
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    public static function accountTargets(): array
    {
        $out = [];
        if (Route::has('login')) {
            $out['login'] = __('auth.login');
        }
        if (Route::has('register')) {
            $out['register'] = __('auth.register');
        }
        if (Route::has('usercp.index')) {
            $out['usercp.index'] = __('nav.usercp');
        }

        return $out;
    }

    /**
     * Position labels for admin UI.
     *
     * @return array<string, string>
     */
    public static function positions(): array
    {
        return [
            'top' => __('menu.position_top'),
            'left' => __('menu.position_left'),
            'right' => __('menu.position_right'),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Alle CMS-Ressourcen / Tabellen für CRUD-Rechte.
     *
     * @var list<string>
     */
    protected array $resourceTables = [
        'users', 'roles', 'permissions',
        'posts', 'events', 'applications', 'ranks', 'clan_members',
        'widget_instances', 'settings', 'forum_categories', 'forum_forums',
        'clan_document_categories', 'clan_documents', 'clan_announcements',
        'clan_rules', 'clan_teams', 'clan_team_members', 'clan_achievements',
        'clan_feedback', 'clan_leaderboard_categories', 'clan_leaderboard_entries',
        'clan_treasury_categories', 'clan_treasury_entries', 'clan_bank_categories',
        'clan_bank_items', 'marketplace_categories', 'marketplace_listings',
        'job_offer_categories', 'job_offers', 'job_applications',
        'wiki_categories', 'wiki_articles', 'gallery_albums', 'gallery_images',
        'download_categories', 'downloads', 'partners', 'polls',
        'newsletter_subscribers', 'slider_slides', 'music_tracks', 'media', 'translations',
        'user_notifications', 'craftable_items', 'item_requests',
        'menu_items', 'discord_quick_commands', 'cms_pages',
    ];

    /**
     * Extra-Rechte für Custom-Pages / Systembereiche.
     *
     * @var list<string>
     */
    protected array $pagePermissions = [
        'access_admin',
        'manage_settings',
        'manage_modules',
        'manage_plugins',
        'manage_system_modules',
        'manage_themes',
        'manage_theme_editor',
        'send_newsletter',
    ];

    protected array $abilities = [
        'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
        'restore', 'restore_any', 'reorder', 'force_delete', 'force_delete_any', 'replicate',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        foreach ($this->pagePermissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        foreach ($this->resourceTables as $table) {
            $label = str_replace('_', ' ', $table);
            foreach ($this->abilities as $ability) {
                Permission::firstOrCreate([
                    'name' => $ability.' '.$label,
                    'guard_name' => $guard,
                ]);
            }
        }

        $all = Permission::query()->where('guard_name', $guard)->get();

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        $superAdmin->syncPermissions($all);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $admin->syncPermissions(
            $all->filter(fn (Permission $p) => ! in_array($p->name, [
                // Rechteverwaltung bleibt Super-Admin
            ], true) && ! str_starts_with($p->name, 'force_delete')
                && ! str_contains($p->name, ' roles')
                && ! str_contains($p->name, ' permissions')
                && $p->name !== 'manage_modules'
                && $p->name !== 'manage_plugins')
        );

        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => $guard]);
        $moderator->syncPermissions(
            $all->filter(function (Permission $p) {
                $name = $p->name;
                if ($name === 'access_admin') {
                    return true;
                }
                foreach ([
                    'applications', 'forum categories', 'forum forums', 'clan feedback',
                    'posts', 'events', 'user notifications', 'polls',
                ] as $area) {
                    if (str_contains($name, $area)) {
                        return ! str_starts_with($name, 'force_delete');
                    }
                }

                return false;
            })
        );

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => $guard]);
        $editor->syncPermissions(
            $all->filter(function (Permission $p) {
                $name = $p->name;
                if ($name === 'access_admin') {
                    return true;
                }
                foreach ([
                    'posts', 'cms pages', 'events', 'wiki categories', 'wiki articles',
                    'slider slides', 'partners', 'media', 'gallery albums', 'gallery images',
                    'download categories', 'downloads', 'menu items',
                ] as $area) {
                    if (str_contains($name, $area)) {
                        return in_array(explode(' ', $name, 2)[0], [
                            'view_any', 'view', 'create', 'update', 'reorder', 'replicate',
                        ], true);
                    }
                }

                return false;
            })
        );

        Role::firstOrCreate(['name' => 'member', 'guard_name' => $guard]);

        $firstUser = User::query()->orderBy('id')->first();
        if ($firstUser && ! $firstUser->hasRole('super-admin')) {
            $firstUser->assignRole('super-admin');
        }
    }
}

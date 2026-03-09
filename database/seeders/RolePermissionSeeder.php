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
     * Liste der Tabellen/Resources, für die Standard-Berechtigungen angelegt werden.
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
        'job_offer_categories', 'job_offers', 'wiki_categories', 'wiki_articles',
        'gallery_albums', 'gallery_images', 'download_categories', 'downloads',
        'partners', 'polls', 'newsletter_subscribers', 'slider_slides',
        'media', 'translations', 'user_notifications',
        'craftable_items', 'item_requests',
    ];

    protected array $abilities = ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'restore_any', 'reorder', 'force_delete', 'force_delete_any', 'replicate'];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        // Einzelberechtigung für Admin-Zugang
        Permission::firstOrCreate(['name' => 'access_admin', 'guard_name' => $guard]);

        // Berechtigungen pro Resource
        foreach ($this->resourceTables as $table) {
            $label = str_replace('_', ' ', $table);
            foreach ($this->abilities as $ability) {
                $name = $ability . ' ' . $label;
                Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
            }
        }

        // Rolle Super-Admin mit allen Rechten
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        $superAdmin->givePermissionTo(Permission::all());

        // Rolle Member für registrierte Frontend-User (keine Admin-Rechte)
        Role::firstOrCreate(['name' => 'member', 'guard_name' => $guard]);

        // Optional: Erstem User Super-Admin zuweisen
        $firstUser = User::first();
        if ($firstUser && ! $firstUser->hasRole('super-admin')) {
            $firstUser->assignRole('super-admin');
        }
    }
}

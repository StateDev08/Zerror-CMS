<?php

namespace App\Support;

/**
 * Deutsche Labels und Gruppen für Spatie-/CMS-Permissions.
 */
class PermissionCatalog
{
    /**
     * @return array<string, string> permission name => label
     */
    public static function labels(): array
    {
        $resourceLabels = [
            'users' => 'Benutzer',
            'roles' => 'Rollen',
            'permissions' => 'Berechtigungen',
            'posts' => 'News',
            'events' => 'Termine',
            'applications' => 'Bewerbungen',
            'ranks' => 'Ränge',
            'clan_members' => 'Clan-Mitglieder',
            'widget_instances' => 'Widgets',
            'settings' => 'Einstellungen (Datensatz)',
            'forum_categories' => 'Forum-Kategorien',
            'forum_forums' => 'Foren',
            'clan_document_categories' => 'Dokument-Kategorien',
            'clan_documents' => 'Dokumente',
            'clan_announcements' => 'Ankündigungen',
            'clan_rules' => 'Clan-Regeln',
            'clan_teams' => 'Teams',
            'clan_team_members' => 'Team-Mitglieder',
            'clan_achievements' => 'Erfolge',
            'clan_feedback' => 'Feedback',
            'clan_leaderboard_categories' => 'Rangliste-Kategorien',
            'clan_leaderboard_entries' => 'Rangliste-Einträge',
            'clan_treasury_categories' => 'Kasse-Kategorien',
            'clan_treasury_entries' => 'Kasse-Einträge',
            'clan_bank_categories' => 'Bank-Kategorien',
            'clan_bank_items' => 'Bank-Items',
            'marketplace_categories' => 'Marktplatz-Kategorien',
            'marketplace_listings' => 'Marktplatz-Anzeigen',
            'job_offer_categories' => 'Job-Kategorien',
            'job_offers' => 'Stellenangebote',
            'job_applications' => 'Job-Bewerbungen',
            'wiki_categories' => 'Wiki-Kategorien',
            'wiki_articles' => 'Wiki-Artikel',
            'gallery_albums' => 'Galerie-Alben',
            'gallery_images' => 'Galerie-Bilder',
            'download_categories' => 'Download-Kategorien',
            'downloads' => 'Downloads',
            'partners' => 'Partner',
            'polls' => 'Umfragen',
            'newsletter_subscribers' => 'Newsletter-Abonnenten',
            'slider_slides' => 'Slider',
            'music_tracks' => 'Musik',
            'media' => 'Medien',
            'translations' => 'Übersetzungen',
            'user_notifications' => 'Benachrichtigungen',
            'craftable_items' => 'Craftbare Items',
            'item_requests' => 'Item-Aufträge',
            'menu_items' => 'Menü',
            'discord_quick_commands' => 'Discord Quick-Befehle',
            'cms_pages' => 'Seiten',
        ];

        $abilityLabels = [
            'view_any' => 'Liste sehen',
            'view' => 'Ansehen',
            'create' => 'Erstellen',
            'update' => 'Bearbeiten',
            'delete' => 'Löschen',
            'delete_any' => 'Mehrere löschen',
            'restore' => 'Wiederherstellen',
            'restore_any' => 'Mehrere wiederherstellen',
            'reorder' => 'Sortieren',
            'force_delete' => 'Endgültig löschen',
            'force_delete_any' => 'Mehrere endgültig löschen',
            'replicate' => 'Duplizieren',
        ];

        $out = [
            'access_admin' => 'Admin-Zugang',
            'manage_settings' => 'Seiteneinstellungen',
            'manage_modules' => 'Module verwalten',
            'manage_plugins' => 'Plugins verwalten',
            'manage_system_modules' => 'System-Module verwalten',
            'manage_themes' => 'Themes wählen',
            'manage_theme_editor' => 'Theme-Editor',
            'send_newsletter' => 'Newsletter senden',
        ];

        foreach ($resourceLabels as $table => $resourceLabel) {
            $slug = str_replace('_', ' ', $table);
            foreach ($abilityLabels as $ability => $abilityLabel) {
                $out[$ability.' '.$slug] = $resourceLabel.': '.$abilityLabel;
            }
        }

        return $out;
    }

    /**
     * @return array<string, list<string>> Gruppe => Permission-Namen
     */
    public static function groups(): array
    {
        $labels = self::labels();
        $groups = [
            'System' => [
                'access_admin',
                'manage_settings',
                'manage_modules',
                'manage_plugins',
                'manage_system_modules',
                'manage_themes',
                'manage_theme_editor',
                'send_newsletter',
            ],
            'Benutzer & Rechte' => [],
            'Inhalte' => [],
            'Clan' => [],
            'Forum & Community' => [],
            'Medien & Downloads' => [],
            'Markt & Jobs' => [],
            'Sonstige' => [],
        ];

        $map = [
            'users' => 'Benutzer & Rechte',
            'roles' => 'Benutzer & Rechte',
            'permissions' => 'Benutzer & Rechte',
            'posts' => 'Inhalte',
            'cms_pages' => 'Inhalte',
            'events' => 'Inhalte',
            'slider_slides' => 'Inhalte',
            'music_tracks' => 'Inhalte',
            'widget_instances' => 'Inhalte',
            'partners' => 'Inhalte',
            'polls' => 'Inhalte',
            'newsletter_subscribers' => 'Inhalte',
            'translations' => 'Inhalte',
            'media' => 'Medien & Downloads',
            'gallery_albums' => 'Medien & Downloads',
            'gallery_images' => 'Medien & Downloads',
            'download_categories' => 'Medien & Downloads',
            'downloads' => 'Medien & Downloads',
            'wiki_categories' => 'Inhalte',
            'wiki_articles' => 'Inhalte',
            'menu_items' => 'Inhalte',
            'applications' => 'Forum & Community',
            'forum_categories' => 'Forum & Community',
            'forum_forums' => 'Forum & Community',
            'clan_feedback' => 'Forum & Community',
            'user_notifications' => 'Forum & Community',
            'ranks' => 'Clan',
            'clan_members' => 'Clan',
            'clan_document_categories' => 'Clan',
            'clan_documents' => 'Clan',
            'clan_announcements' => 'Clan',
            'clan_rules' => 'Clan',
            'clan_teams' => 'Clan',
            'clan_team_members' => 'Clan',
            'clan_achievements' => 'Clan',
            'clan_leaderboard_categories' => 'Clan',
            'clan_leaderboard_entries' => 'Clan',
            'clan_treasury_categories' => 'Clan',
            'clan_treasury_entries' => 'Clan',
            'clan_bank_categories' => 'Clan',
            'clan_bank_items' => 'Clan',
            'marketplace_categories' => 'Markt & Jobs',
            'marketplace_listings' => 'Markt & Jobs',
            'job_offer_categories' => 'Markt & Jobs',
            'job_offers' => 'Markt & Jobs',
            'job_applications' => 'Markt & Jobs',
            'craftable_items' => 'Markt & Jobs',
            'item_requests' => 'Markt & Jobs',
            'discord_quick_commands' => 'System',
            'settings' => 'System',
        ];

        foreach (array_keys($labels) as $name) {
            if (in_array($name, $groups['System'], true)) {
                continue;
            }
            if (! str_contains($name, ' ')) {
                $groups['Sonstige'][] = $name;
                continue;
            }
            $table = str_replace(' ', '_', substr($name, strpos($name, ' ') + 1));
            $group = $map[$table] ?? 'Sonstige';
            $groups[$group][] = $name;
        }

        return array_filter($groups, fn (array $items) => $items !== []);
    }
}

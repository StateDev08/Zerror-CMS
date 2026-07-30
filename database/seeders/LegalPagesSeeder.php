<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'impressum',
                'title' => 'Impressum',
                'content' => '<p>Hier Impressumsangaben eintragen (Name, Anschrift, Kontakt).</p><p>Im ACP unter <strong>Inhalte → News &amp; Seiten</strong> bearbeiten.</p>',
            ],
            [
                'slug' => 'datenschutz',
                'title' => 'Datenschutz',
                'content' => '<p>Hier die Datenschutzerklärung eintragen.</p><p>Im ACP unter <strong>Inhalte → News &amp; Seiten</strong> bearbeiten.</p>',
            ],
            [
                'slug' => 'agb',
                'title' => 'AGB',
                'content' => '<p>Hier die Allgemeinen Geschäftsbedingungen / Nutzungsbedingungen eintragen.</p><p>Im ACP unter <strong>Inhalte → News &amp; Seiten</strong> bearbeiten.</p>',
            ],
            [
                'slug' => 'cookies',
                'title' => 'Cookies',
                'content' => '<p>Hier die Cookie-Hinweise eintragen.</p><p>Im ACP unter <strong>Inhalte → News &amp; Seiten</strong> bearbeiten.</p>',
            ],
        ];

        foreach ($pages as $page) {
            // Nur fehlende Seiten anlegen – bestehende Custom-Texte nie überschreiben.
            Post::query()->firstOrCreate(
                ['slug' => $page['slug'], 'type' => 'page'],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'published' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $top = [
            ['label' => 'Startseite', 'link' => 'home', 'sort_order' => 0],
            ['label' => 'News', 'link' => 'news.index', 'sort_order' => 10],
            ['label' => 'Mitglieder', 'link' => 'roster.index', 'sort_order' => 20],
            ['label' => 'Kalender', 'link' => 'calendar.index', 'sort_order' => 30],
            ['label' => 'Server', 'link' => 'servers.index', 'sort_order' => 35],
            ['label' => 'Wiki', 'link' => 'wiki.index', 'sort_order' => 40],
            ['label' => 'Bewerbung', 'link' => 'apply.index', 'sort_order' => 50],
            ['label' => 'Forum', 'link' => 'forum.index', 'sort_order' => 60],
        ];

        foreach ($top as $item) {
            MenuItem::firstOrCreate(
                ['position' => 'top', 'link' => $item['link']],
                array_merge($item, ['position' => 'top', 'is_visible' => true])
            );
        }

        $left = [
            ['label' => 'Startseite', 'link' => 'home', 'sort_order' => 0],
            ['label' => 'News', 'link' => 'news.index', 'sort_order' => 10],
            ['label' => 'Forum', 'link' => 'forum.index', 'sort_order' => 15],
            ['label' => 'Mitglieder', 'link' => 'roster.index', 'sort_order' => 20],
            ['label' => 'Kalender', 'link' => 'calendar.index', 'sort_order' => 30],
            ['label' => 'Server', 'link' => 'servers.index', 'sort_order' => 35],
            ['label' => 'Bewerbung', 'link' => 'apply.index', 'sort_order' => 40],
            ['label' => 'Wiki', 'link' => 'wiki.index', 'sort_order' => 50],
        ];

        foreach ($left as $item) {
            MenuItem::firstOrCreate(
                ['position' => 'left', 'link' => $item['link']],
                array_merge($item, ['position' => 'left', 'is_visible' => true])
            );
        }

        $right = [
            ['label' => 'Clan-Bank', 'link' => 'clan-bank.index', 'sort_order' => 10],
            ['label' => 'Schatzkammer', 'link' => 'clan-treasury.index', 'sort_order' => 20],
            ['label' => 'Marktplatz', 'link' => 'marketplace.index', 'sort_order' => 30],
            ['label' => 'Jobs', 'link' => 'jobs.index', 'sort_order' => 40],
            ['label' => 'Newsletter', 'link' => 'newsletter.index', 'sort_order' => 50],
        ];

        foreach ($right as $item) {
            MenuItem::firstOrCreate(
                ['position' => 'right', 'link' => $item['link']],
                array_merge($item, ['position' => 'right', 'is_visible' => true])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $left = [
            ['label' => 'Startseite', 'link' => 'home', 'sort_order' => 0],
            ['label' => 'News', 'link' => 'news.index', 'sort_order' => 10],
            ['label' => 'Roster', 'link' => 'roster.index', 'sort_order' => 20],
            ['label' => 'Kalender', 'link' => 'calendar.index', 'sort_order' => 30],
        ];

        foreach ($left as $item) {
            MenuItem::firstOrCreate(
                ['position' => 'left', 'link' => $item['link']],
                array_merge($item, ['position' => 'left', 'is_visible' => true])
            );
        }
    }
}

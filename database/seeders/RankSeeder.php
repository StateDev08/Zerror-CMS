<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RankSeeder extends Seeder
{
    /**
     * Clan-Ränge für Forum-Berechtigungen & Mitgliederliste.
     * Niedrigere order = höherer Rang (wie ForumController prüft).
     */
    public function run(): void
    {
        $ranks = [
            ['name' => 'Anführer', 'slug' => 'anfuehrer', 'color' => '#b8954a', 'order' => 0],
            ['name' => 'Offizier', 'slug' => 'offizier', 'color' => '#c4a35a', 'order' => 10],
            ['name' => 'Veteran', 'slug' => 'veteran', 'color' => '#6d7f8c', 'order' => 20],
            ['name' => 'Mitglied', 'slug' => 'mitglied', 'color' => '#9aa3b0', 'order' => 30],
            ['name' => 'Rekrut', 'slug' => 'rekrut', 'color' => '#7a8494', 'order' => 40],
        ];

        foreach ($ranks as $rank) {
            Rank::updateOrCreate(
                ['slug' => $rank['slug']],
                [
                    'name' => $rank['name'],
                    'color' => $rank['color'],
                    'order' => $rank['order'],
                    'slug' => $rank['slug'] ?: Str::slug($rank['name']),
                ]
            );
        }
    }
}

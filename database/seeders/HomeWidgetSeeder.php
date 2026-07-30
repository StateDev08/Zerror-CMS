<?php

namespace Database\Seeders;

use App\Models\WidgetInstance;
use App\Widgets\WidgetRegistry;
use Illuminate\Database\Seeder;

class HomeWidgetSeeder extends Seeder
{
    public function run(): void
    {
        WidgetInstance::query()->where('slot', 'home')->update(['slot' => 'left']);
        WidgetInstance::query()->where('slot', 'sidebar')->update(['slot' => 'right']);

        $registry = app(WidgetRegistry::class);

        $defaults = [
            ['slot' => 'left', 'widget_key' => 'latest_news', 'order' => 10, 'config' => ['limit' => 5]],
            ['slot' => 'left', 'widget_key' => 'discord', 'order' => 20, 'config' => []],
            ['slot' => 'left', 'widget_key' => 'music_player', 'order' => 25, 'config' => []],
            ['slot' => 'left', 'widget_key' => 'social_links', 'order' => 30, 'config' => []],
            ['slot' => 'right', 'widget_key' => 'upcoming_events', 'order' => 10, 'config' => ['limit' => 5]],
            ['slot' => 'right', 'widget_key' => 'server_status', 'order' => 20, 'config' => ['limit' => 5]],
            ['slot' => 'right', 'widget_key' => 'newsletter', 'order' => 30, 'config' => []],
            ['slot' => 'right', 'widget_key' => 'donation', 'order' => 40, 'config' => []],
            ['slot' => 'right', 'widget_key' => 'latest_forum_posts', 'order' => 50, 'config' => ['limit' => 5]],
        ];

        foreach ($defaults as $item) {
            if (! $registry->get($item['widget_key'])) {
                continue;
            }
            WidgetInstance::firstOrCreate(
                ['slot' => $item['slot'], 'widget_key' => $item['widget_key']],
                ['order' => $item['order'], 'config' => $item['config']]
            );
        }
    }
}

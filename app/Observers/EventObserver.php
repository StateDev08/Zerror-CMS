<?php

namespace App\Observers;

use App\Models\Event;
use App\Support\DiscordNotifier;

class EventObserver
{
    public function created(Event $event): void
    {
        $this->notifyDiscord($event);
    }

    public function updated(Event $event): void
    {
        if ($event->wasChanged(['title', 'starts_at', 'ends_at', 'description'])) {
            $this->notifyDiscord($event);
        }
    }

    private function notifyDiscord(Event $event): void
    {
        if (! config('discord_bot.enabled') || ! config('discord_bot.events_webhook_url') || ! $event->visible) {
            return;
        }

        $appUrl = rtrim(config('discord_bot.app_url'), '/');
        $starts = $event->starts_at->format('d.m.Y H:i');
        $ends = $event->ends_at ? $event->ends_at->format('d.m.Y H:i') : '—';
        $location = $event->location ?? '—';
        $type = $event->type ?? 'Sonstiges';

        $title = '📅 Event: ' . $event->title;
        $description = \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 500);
        $embeds = [[
            'title' => $event->title,
            'description' => $description,
            'color' => 0x57F287,
            'fields' => [
                ['name' => 'Start', 'value' => $starts, 'inline' => true],
                ['name' => 'Ende', 'value' => $ends, 'inline' => true],
                ['name' => 'Typ', 'value' => $type, 'inline' => true],
                ['name' => 'Ort', 'value' => $location, 'inline' => false],
            ],
            'footer' => ['text' => config('clan.name', 'ZerroCMS') . ' Events'],
        ]];

        DiscordNotifier::postToEvents($title, $embeds);
    }
}

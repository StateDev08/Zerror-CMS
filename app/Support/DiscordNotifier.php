<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordNotifier
{
    public static function postToShop(string $content, array $embeds = []): bool
    {
        $url = config('discord_bot.shop_webhook_url');
        if (! $url) {
            return false;
        }
        return self::postWebhook($url, $content, $embeds);
    }

    public static function postToEvents(string $content, array $embeds = []): bool
    {
        $url = config('discord_bot.events_webhook_url');
        if (! $url) {
            return false;
        }
        return self::postWebhook($url, $content, $embeds);
    }

    private static function postWebhook(string $url, string $content, array $embeds = []): bool
    {
        try {
            $payload = ['content' => $content];
            if ($embeds !== []) {
                $payload['embeds'] = $embeds;
            }
            $response = Http::timeout(5)->post($url, $payload);
            if (! $response->successful()) {
                Log::warning('Discord webhook failed', ['url' => preg_replace('#^https?://[^/]+#', '', $url), 'status' => $response->status()]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::warning('Discord webhook error: ' . $e->getMessage());
            return false;
        }
    }
}

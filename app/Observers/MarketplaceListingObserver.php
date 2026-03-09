<?php

namespace App\Observers;

use App\Models\MarketplaceListing;
use App\Support\DiscordNotifier;

class MarketplaceListingObserver
{
    public function created(MarketplaceListing $listing): void
    {
        if ($listing->published) {
            $this->notifyDiscord($listing);
        }
    }

    public function updated(MarketplaceListing $listing): void
    {
        if ($listing->wasChanged('published') && $listing->published) {
            $this->notifyDiscord($listing);
        }
    }

    private function notifyDiscord(MarketplaceListing $listing): void
    {
        if (! config('discord_bot.enabled') || ! config('discord_bot.shop_webhook_url')) {
            return;
        }

        $appUrl = rtrim(config('discord_bot.app_url'), '/');
        $link = $appUrl . '/marketplace/' . $listing->slug;
        $category = $listing->category?->name ?? '—';
        $price = match ($listing->price_type) {
            'free' => 'Kostenlos',
            'fixed' => $listing->price_value ? number_format((float) $listing->price_value, 2, ',', '') . ' €' : '—',
            default => 'VB',
        };

        $title = '🛒 Neues Angebot: ' . $listing->title;
        $description = \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 300);
        $embeds = [[
            'title' => $listing->title,
            'url' => $link,
            'description' => $description,
            'color' => 0x5865F2,
            'fields' => [
                ['name' => 'Kategorie', 'value' => $category, 'inline' => true],
                ['name' => 'Preis', 'value' => $price, 'inline' => true],
                ['name' => 'Link', 'value' => $link, 'inline' => false],
            ],
            'footer' => ['text' => config('clan.name', 'ZerroCMS') . ' Marketplace'],
        ]];

        DiscordNotifier::postToShop($title, $embeds);
    }
}

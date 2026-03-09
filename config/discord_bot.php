<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Discord Bot Integration
    |--------------------------------------------------------------------------
    | Webhooks: Laravel postet z. B. neue Shop-Angebote und Events in diese
    | Discord-Channels. Bot-API: Token für den Bot, API-Key für Bot → Laravel.
    */
    'enabled' => (bool) env('DISCORD_BOT_ENABLED', false),

    'bot_token' => env('DISCORD_BOT_TOKEN'),

    /** Webhook-URLs: Bei neuem Marketplace-Eintrag / neuem Event wird hier gepostet. */
    'shop_webhook_url' => env('DISCORD_SHOP_WEBHOOK_URL'),
    'events_webhook_url' => env('DISCORD_EVENTS_WEBHOOK_URL'),

    /**
     * API-Key: Der Discord-Bot ruft Laravel-API mit diesem Key auf (Header: X-API-Key).
     * Für Slash-Commands (Quick-Befehle) und Spieler-Abfragen.
     */
    'api_key' => env('DISCORD_BOT_API_KEY'),

    /** Basis-URL der Laravel-App (für Links in Discord-Nachrichten). */
    'app_url' => env('APP_URL', 'https://example.com'),
];

<?php

return [
    'name' => env('CLAN_NAME', 'ZerroCMS'),
    'theme' => env('CLAN_THEME', 'default'),
    'logo' => env('CLAN_LOGO'),
    'banner' => env('CLAN_BANNER'),
    'discord_webhook_url' => env('DISCORD_WEBHOOK_URL'),
    'discord_invite_url' => env('DISCORD_INVITE_URL'),
    'application_notify_email' => env('APPLICATION_NOTIFY_EMAIL'),
    'donation_url' => env('DONATION_URL'),

    /*
    | Footer legal pages (slug => translation key or label). Only these are shown.
    | Omit or override in config to change order or visibility.
    */
    'footer_pages' => [
        'impressum' => 'nav.impressum',
        'datenschutz' => 'nav.datenschutz',
        'agb' => 'nav.agb',
        'cookies' => 'nav.cookies',
    ],

    /*
    | Optional: custom nav entries (array of route + label key). If non-empty, theme nav can use this instead of default.
    */
    'nav_entries' => [],
];

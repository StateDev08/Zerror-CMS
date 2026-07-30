<?php
/**
 * Add help texts to module/plugin config schemas.
 */
$root = dirname(__DIR__);

$moduleHelp = [
    'cookie_banner' => [
        'text' => 'Kurztext, der im Cookie-Hinweis angezeigt wird.',
        'privacy_url' => 'Link zur Datenschutzerklärung (z. B. /page/datenschutz).',
        'accept_label' => 'Beschriftung des Akzeptieren-Buttons.',
    ],
    'discord' => [
        'invite_url' => 'Öffentlicher Discord-Einladungslink (https://discord.gg/…).',
        'button_text' => 'Text auf dem Join-Button im Frontend.',
        'server_id' => 'Discord-Server-ID für das Widget (optional).',
    ],
    'discord_invite' => [
        'invite_url' => 'Discord-Einladungslink, der im Sidebar-Widget genutzt wird.',
        'button_text' => 'Beschriftung des Einladungs-Buttons.',
    ],
    'newsletter_box' => [
        'title' => 'Überschrift der Newsletter-Box.',
        'intro' => 'Kurzer Hinweistext unter der Überschrift.',
        'button_text' => 'Text des Anmelde-Buttons.',
    ],
    'server_status' => [
        'host' => 'Hostname oder IP des Spielservers.',
        'port' => 'Abfrage-Port des Servers.',
        'label' => 'Anzeigename im Widget (z. B. Survival #1).',
        'query_type' => 'Protokoll/Typ der Statusabfrage, falls unterstützt.',
    ],
    'social_links' => [
        'twitter' => 'Vollständige URL zum X/Twitter-Profil.',
        'youtube' => 'Vollständige URL zum YouTube-Kanal.',
        'twitch' => 'Vollständige URL zum Twitch-Kanal.',
        'discord' => 'Vollständige URL / Invite zum Discord.',
        'facebook' => 'Vollständige URL zur Facebook-Seite.',
        'instagram' => 'Vollständige URL zum Instagram-Profil.',
    ],
    'teamspeak' => [
        'host' => 'TeamSpeak-Serveradresse (ohne ts3server://).',
        'port' => 'Voice-Port, Standard meist 9987.',
        'button_text' => 'Text des Connect-Buttons.',
    ],
    'ts3_viewer' => [
        'host' => 'ServerQuery-Host oder Anzeige-Adresse.',
        'port' => 'Query- oder Voice-Port je nach Setup.',
        'nickname' => 'Anzeigename in der Viewer-Liste.',
        'password' => 'Optionales Query-Passwort (geheim halten).',
    ],
    'twitch' => [
        'channel' => 'Twitch-Kanalname ohne URL (z. B. meinclan).',
        'parent' => 'Parent-Domain für Embeds (meist deine Domain ohne https).',
    ],
    'twitter' => [
        'handle' => 'X/Twitter-Handle ohne @.',
        'widget_id' => 'Optionale Widget-ID, falls vorhanden.',
    ],
    'voice_chat' => [
        'url' => 'Link zu Discord/Mumble/TS o. Ä.',
        'label' => 'Beschriftung des Voice-Chat-Buttons.',
        'platform' => 'Plattformname zur Anzeige (Discord, TeamSpeak, …).',
    ],
];

$pluginHelp = [
    'analytics' => [
        'tracking_id' => 'Google Analytics 4 Measurement-ID, z. B. G-XXXXXXXXXX.',
        'enabled' => 'Tracking-Skript nur laden, wenn aktiv.',
    ],
    'backup_reminder' => [
        'days' => 'Nach wie vielen Tagen ohne Backup der Hinweis erscheint.',
        'enabled' => 'Admin-Hinweis im Panel ein- oder ausschalten.',
    ],
    'cookie_consent' => [
        'banner_text' => 'Text im Cookie-Banner für Besucher.',
        'privacy_url' => 'URL zur Datenschutzerklärung.',
        'accept_button' => 'Text des Zustimmen-Buttons.',
        'enabled' => 'Banner im Frontend anzeigen.',
    ],
    'custom_css' => [
        'css' => 'Eigenes CSS, das im &lt;head&gt; eingebunden wird. Nur CSS, kein HTML.',
        'enabled' => 'Custom-CSS laden.',
    ],
    'discord_embed' => [
        'server_id' => 'Discord-Server-ID für das offizielle Widget.',
        'theme' => 'Widget-Darstellung (dark/light), falls unterstützt.',
        'enabled' => 'Discord-Embed aktivieren.',
    ],
    'donation' => [
        'url' => 'Link zu PayPal, Ko-fi, Patreon o. Ä.',
        'label' => 'Button- oder Link-Text.',
        'enabled' => 'Spenden-Hinweis aktivieren.',
    ],
    'event_reminder' => [
        'hours_before' => 'Stunden vor dem Event für die Erinnerung.',
        'enabled' => 'Event-Erinnerungen aktivieren.',
    ],
    'maintenance' => [
        'enabled' => 'Wartungsmodus: Frontend nur für Admins.',
        'message' => 'Nachricht, die Besuchern angezeigt wird.',
    ],
    'partner_slider' => [
        'autoplay' => 'Partner-Slider automatisch durchlaufen.',
        'interval' => 'Wechselintervall in Millisekunden.',
        'enabled' => 'Partner-Slider aktivieren.',
    ],
    'seo_meta' => [
        'default_title' => 'Standard-Seitentitel, wenn keine spezifische SEO-Angabe existiert.',
        'default_description' => 'Standard-Meta-Description für Suchmaschinen.',
        'keywords' => 'Optionale Keywords (kommagetrennt).',
        'enabled' => 'SEO-Meta-Tags ausgeben.',
    ],
    'twitch_embed' => [
        'channel' => 'Twitch-Kanal für das Embed.',
        'parent' => 'Parent-Domain ohne https (Pflicht für Twitch-Embeds).',
        'enabled' => 'Twitch-Embed aktivieren.',
    ],
];

function patchSchema(array $schema, array $helps): array
{
    $out = [];
    foreach ($schema as $item) {
        if (! is_array($item)) {
            continue;
        }
        // associative style key => def
        if (! isset($item['key']) && ! isset($item['type'])) {
            foreach ($item as $k => $def) {
                if (! is_array($def)) {
                    continue;
                }
                $def['key'] = $def['key'] ?? $k;
                if (isset($helps[$k]) && empty($def['help'])) {
                    $def['help'] = $helps[$k];
                }
                if (empty($def['label'])) {
                    $def['label'] = ucwords(str_replace('_', ' ', (string) $def['key']));
                }
                $out[] = $def;
            }
            continue;
        }
        $key = $item['key'] ?? '';
        if ($key !== '' && isset($helps[$key]) && empty($item['help'])) {
            $item['help'] = $helps[$key];
        }
        if (empty($item['label']) && $key !== '') {
            $item['label'] = ucwords(str_replace('_', ' ', $key));
        }
        $out[] = $item;
    }

    return $out;
}

foreach (glob($root.'/modules/*/config.json') ?: [] as $path) {
    $name = basename(dirname($path));
    $schema = json_decode(file_get_contents($path), true);
    if (! is_array($schema)) {
        continue;
    }
    $patched = patchSchema($schema, $moduleHelp[$name] ?? []);
    file_put_contents($path, json_encode($patched, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
    echo "module $name\n";
}

foreach (glob($root.'/plugins/*/plugin.json') ?: [] as $path) {
    $data = json_decode(file_get_contents($path), true);
    if (! is_array($data) || empty($data['configSchema']) || ! is_array($data['configSchema'])) {
        echo "skip plugin ".basename(dirname($path))."\n";
        continue;
    }
    $name = $data['name'] ?? basename(dirname($path));
    $data['configSchema'] = patchSchema($data['configSchema'], $pluginHelp[$name] ?? []);
    if (empty($data['label'])) {
        $data['label'] = ucwords(str_replace('_', ' ', (string) $name));
    }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
    echo "plugin $name\n";
}

echo "done\n";

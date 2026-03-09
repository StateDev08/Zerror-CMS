# ZerroCMS Discord-Bot

Dieser Bot ergänzt ZerroCMS um **Slash-Commands** in Discord (z. B. gespeicherte Quick-Befehle wie „Spitzhacke“) und **Spieler-Verknüpfung**. Shop-Angebote und Events werden automatisch von Laravel in Discord-Channels gepostet (über Webhooks).

## Voraussetzungen

- Node.js 18+
- ZerroCMS mit aktivierter Discord-Bot-Config und API-Key

## Einrichtung

1. **Bot im Discord Developer Portal anlegen**
   - https://discord.com/developers/applications → New Application
   - Bot erstellen, Token kopieren
   - OAuth2 → URL Generator: Scopes `bot`, `applications.commands`; Bot Permissions z. B. „Send Messages“, „Use Slash Commands“
   - Bot zum Server einladen

2. **Laravel (ZerroCMS)**
   - `.env`: `DISCORD_BOT_ENABLED=true`, `DISCORD_BOT_TOKEN=…`, `DISCORD_BOT_API_KEY=…` (starker Zufallswert)
   - Webhooks für Shop- und Events-Channel anlegen (Channel → Einstellungen → Integrationen → Webhooks):  
     `DISCORD_SHOP_WEBHOOK_URL`, `DISCORD_EVENTS_WEBHOOK_URL`
   - Migrationen: `php artisan migrate`
   - Im Admin: **Discord → Quick-Befehle** anlegen (z. B. Name `spitzhacke`, Antworttext „Wer braucht eine Spitzhacke? Hier! …“)

3. **Bot starten**
   ```bash
   cd discord-bot
   cp .env.example .env
   # .env anpassen: DISCORD_BOT_TOKEN, LARAVEL_API_URL, DISCORD_BOT_API_KEY
   npm install
   npm start
   ```

## Slash-Commands

- **`/befehl run name:<name>`** – Führt einen auf der Website angelegten Quick-Befehl aus (z. B. `name:spitzhacke`).
- **`/befehl list`** – Listet alle verfügbaren Quick-Befehle (ephemeral).
- **`/spieler`** – Zeigt den mit dem Discord-Account verknüpften Website-User (wenn verknüpft).

## Shop & Events in Discord

- **Neue Marketplace-Anzeige** (oder Veröffentlichung): Laravel postet automatisch in den Channel, dessen Webhook unter `DISCORD_SHOP_WEBHOOK_URL` eingetragen ist.
- **Neues/geändertes Event**: Laravel postet unter `DISCORD_EVENTS_WEBHOOK_URL`.

Diese Funktionen laufen ohne den Node-Bot (nur Laravel + Webhooks). Der Bot wird nur für Slash-Commands und Spieler-Abfrage benötigt.

## Optional: Nur einen Server (Guild)

In `.env` `DISCORD_GUILD_ID=deine_guild_id` setzen – dann werden Slash-Commands nur in diesem Server registriert (sofort sichtbar, kein globales Warten).

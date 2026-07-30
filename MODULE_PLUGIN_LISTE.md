# Gelöschte Module & Plugins (Archiv)

Stand der Löschung: **2026-07-23**  
Projekt: ZerroCMS

Kurzarchiv – was entfernt wurde und warum. Die Zukunftsplanung steht in [`ROADMAP.md`](ROADMAP.md).

---

## Warum gelöscht?

- Viele **Doppelungen** (Discord ×3, Cookie ×2, Voice/TS ×3, Twitch ×2)
- Site-weite Features lagen parallel in **Settings + Plugin** (Cookies, Wartung, SEO)
- Kein sauberer Installer – nur manuelles Ablegen + DB-Flags
- Für Community-Admins unübersichtlich

---

## Module (11) – entfernt

| Key | Kurz | Problem |
|-----|------|---------|
| `cookie_banner` | Cookie-Widget | SSOT jetzt: Einstellungen → Cookies |
| `discord` | Discord-Widget | sinnvoll – später **ein** Discord-Modul |
| `discord_invite` | nur Invite-Button | Duplikat zu `discord` |
| `newsletter_box` | Newsletter-Box | sinnvoll – neu planen |
| `server_status` | Gameserver | Kern für Gaming-Communities – neu planen |
| `social_links` | Social-Links | sinnvoll – neu planen |
| `teamspeak` | TS-Button | aufgehen in Voice |
| `ts3_viewer` | TS-Viewer | aufgehen in Voice |
| `twitch` | Twitch-Link | aufgehen in Social / Stream |
| `twitter` | X/Twitter | aufgehen in Social |
| `voice_chat` | generischer Voice-Link | Duplikat zu TS/Discord |

## Plugins (11) – entfernt

| Key | Kurz | Problem |
|-----|------|---------|
| `analytics` | Tracking | sinnvoll – neu |
| `backup_reminder` | ACP-Hinweis | optional / Core-Hinweis |
| `cookie_consent` | Cookie-Banner | **nicht neu** – Settings |
| `custom_css` | Extra-CSS | sinnvoll – neu |
| `discord_embed` | Discord Embed | Duplikat zu Discord-Modul |
| `donation` | Spenden | sinnvoll – neu |
| `event_reminder` | Event-Box | oft Core-Widget → prüfen |
| `maintenance` | Wartung | **nicht neu** – Settings → Zugang |
| `partner_slider` | Partner-Logos | sinnvoll – neu |
| `seo_meta` | Meta-Keywords | Basis-SEO = Settings; Keywords optional |
| `twitch_embed` | Stream-Embed | mit Social/Stream bündeln |

## Was blieb

- Core-Widgets: `latest_news`, `upcoming_events`, `latest_forum_posts`
- ACP-Menü / Navigation
- Settings als SSOT für Cookies, SEO-Basis, Wartung
- PHP-Klassen unter `app/Widgets` / `app/Providers` (Orphans) – Aufräumen → siehe Roadmap

→ Weiter: **[`ROADMAP.md`](ROADMAP.md)**

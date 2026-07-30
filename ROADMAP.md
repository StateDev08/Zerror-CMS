# ZerroCMS – Roadmap

**Ziel:** Ein Community-CMS, das sich angenehm anfühlt – klar, hilfreich, ohne Doppelungen.  
Admins sollen in wenigen Minuten wissen, *wo* etwas steuert und *wie* man es einrichtet.

Stand: 2026-07-23

---

## Leitbild

| Prinzip | Bedeutung |
|---------|-----------|
| **Eine Wahrheit** | Site-weite Optionen nur in **Einstellungen** (Cookies, SEO-Basis, Wartung, Discord-Invite, …). |
| **Keine Doppelungen** | Ein Feature = ein Modul *oder* ein Plugin – nicht beides. |
| **Community first** | Gaming-/Clan-Communities: Discord, Server, News, Events, Bewerbungen – ohne Technik-Chaos. |
| **Hilfe sichtbar** | Jede ACP-Seite hat Kurzhilfe; es gibt Onboarding + kurze Tutorials. |
| **Erweiterbar** | Module/Plugins per **Paket-Installer** (ZIP) – nicht nur manuell per FTP. |

### Modul vs. Plugin (klar trennen)

| | **Modul** | **Plugin** |
|---|-----------|------------|
| Zweck | Sichtbare Community-Bausteine (Widgets, Seiten) | System-/Integrations-Extras |
| Beispiele | Discord, Serverstatus, Social, Spenden | Analytics, Custom CSS, Partner-Slider |
| Ort | `modules/` + Sidebar-/Seiten-Widgets | `plugins/` + Head/Body/ACP-Hinweise |

---

## Phase 0 – Aufräumen (jetzt / kurzfristig)

- [x] Doppelte Cookie-/Wartungs-/SEO-Steuerung → Settings als SSOT
- [x] Alte Module/Plugins-Verzeichnisse + DB gelöscht (siehe `MODULE_PLUGIN_LISTE.md`)
- [x] Orphan-Code entfernt: alle `*ModuleServiceProvider` / `*PluginServiceProvider`, Modul-/Plugin-Widgets + Views, Filament-`BackupReminderWidget`
- [x] Bewusst behalten für Phase 3: `ServerStatusService` + ACP Gameserver-Seite, `DiscordWidgetApi` (Support ohne Paket)
- [x] Core-Widgets bleiben: `latest_news`, `upcoming_events`, `latest_forum_posts`
- [x] Installer entkoppelt: keine hartcodierten Default-Modul-/Plugin-Listen; leerer Zustand im Finish-Schritt
- [x] ACP Module-/Plugins: erklärter Leerzustand + Hinweis auf Paket-Installer

---

## Phase 1 – Angenehme Nutzung & Hilfe

Ziel: Weniger Rätselraten, mehr „ich weiß, was ich tun soll“.

### 1.1 Onboarding & Hilfe im ACP

- [x] **Erststart-Checkliste** (Dashboard-Widget): Logo → Menü → Startseite → Discord → erster News-Post
- [x] Einheitliche leere Zustände (Widgets, Menü; Module/Plugins/Themes bereits vorhanden)
- [x] Kontext-Links: Einstellungen ↔ Themes ↔ Widgets ↔ Module ↔ Paket-Installer


### 1.3 UX-Vereinfachung

- [x] Fachjargon: Glossar Modul vs. Plugin (Module/Plugins/Paket-Installer)
- [x] Weniger parallele Orte: Discord-URL nur unter Kontakt & Social (Invite gespiegelt)
- [x] Speichern mit Erfolgsmeldung (inkl. Theme-Editor); Wartung mit Warnhinweis + Speicher-Hinweis

---

## Phase 2 – Paket-Installer (Module, Plugins, Widgets, Themes)

Ein zentraler ZIP-Installer für alle Pakettypen.

### Ablauf

1. ACP → System → **Paket-Installer** → ZIP hochladen  
2. Typ automatisch (oder manuell): `module.json` / `plugin.json` / `widget.json` / `theme.json`  
3. Validierung + Entpacken nach `modules/`, `plugins/`, `widgets/` bzw. `themes/`  
4. Module/Plugins: DB-Eintrag (`enabled` wählbar)  
5. Verwalten unter Module / Plugins / Widgets / Themes  

- [x] Einheitlicher `PackageInstaller` (Modul/Plugin/Widget/Theme)
- [x] ACP-Seite **Paket-Installer** + Link im System-Menü
- [x] Deinstallieren (Ordner + DB) von Module-/Plugins-Seite
- [x] Überschreiben bei gleichem Key
- [x] Beispiel-Skeletons: `resources/{module,plugin,widget,theme}-skeletons`
- [x] Widget-Pakete aus `widgets/{id}/` laden (`WidgetPackageManager`)

---

## Phase 3 – Sinnvolle Module & Plugins neu (ohne Doppelungen)

Nur bauen, was Communities wirklich brauchen. Keine Legacy-Parallelwelten.

### Geplante Module (Widgets / Community-Features)

| Key (Vorschlag) | Inhalt | Ersetzt früher | Status |
|-----------------|--------|----------------|--------|
| `discord` | **Ein** Discord-Widget (Live/Invite, Guild optional); Invite-Fallback aus Einstellungen | `discord`, `discord_invite`, `discord_embed` | [x] |
| `server_status` | Gameserver-Liste + Widget + `/servers` | `server_status` | [x] |
| `social_links` | Discord/YouTube/Twitch/X/Instagram/… aus Settings + lokale Overrides | `social_links`, `twitter`, `twitch` (nur Links) | [x] |
| `voice` | TeamSpeak/Mumble/generisch – **ein** Voice-Widget | `teamspeak`, `ts3_viewer`, `voice_chat` | [x] |
| `newsletter` | Sidebar-Anmeldebox | `newsletter_box` | [x] |
| `donation` | Spenden-Button/-Box | Plugin `donation` → besser als **Modul** (sichtbar) | [x] |
| `stream` | Optional: Twitch-/YouTube-Embed **oder** nur Link (konfigurierbar) | `twitch_embed`, Link-Teil von `twitch` | [x] |

### Geplante Plugins (System / Integration)

| Key (Vorschlag) | Inhalt | Ersetzt früher | Status |
|-----------------|--------|----------------|--------|
| `analytics` | GA4 / Plausible / Umami / Custom | `analytics` | [x] |
| `custom_css` | Zusätzliches CSS | `custom_css` | [x] |
| `partners` | Partner-Logo-Slider | `partner_slider` | [x] |
| `seo_extra` | Nur Keywords / Spezial-Meta – **kein** zweites Description-Feld | `seo_meta` (eingeschränkt) | [x] |

### Erledigt (Maximalausbau)

- [x] Widget-Instanzen gewiped; Core-Widgets (`latest_news`, `upcoming_events`, `latest_forum_posts`) ausgebaut
- [x] Alle 7 Module unter `modules/` + Skeletons
- [x] Alle 4 Plugins unter `plugins/` + Skeletons
- [x] DB aktiviert + Default-Sidebar-Layout
- [x] Gameserver-ACP / `/servers` / Newsletter / Partner-Core wiederverwendet (keine Doppel-Routen)

### Bewusst **nicht** neu als Plugin/Modul

| Feature | Stattdessen |
|---------|-------------|
| Cookie-Banner | Einstellungen → Cookies |
| Wartungsmodus | Einstellungen → Zugang |
| Basis-SEO (Title/Description) | Einstellungen → SEO |
| Event-Liste „nächste Termine“ | Core-Widget `upcoming_events` (ggf. ausbauen) |
| Backup-Erinnerung | optional später kleines Core-ACP-Widget, kein Pflicht-Plugin |

### Reihenfolge beim Neuaufbau

1. Paket-Installer (Phase 2) – sonst wieder nur manuelles Chaos  
2. `discord` + Einstellungen-Invite (häufigster Community-Need)  
3. `server_status`  
4. `social_links`  
5. `donation`, `newsletter`, `voice`  
6. Plugins: `analytics`, `custom_css`, `partners`  
7. `stream` / `seo_extra` nach Bedarf  

---

## Phase 4 – Feinschliff Community-Erlebnis

- [ ] Standard-Widget-Layout nach Installation (sinnvolle Defaults links/rechts)
- [ ] „Community-Vorlage“: Menü-Punkte News, Forum, Wiki, Bewerbung, Discord vorbefüllt
- [ ] Kurze Tooltips an kritischen Schaltern
- [ ] Health-Check: fehlendes Logo, leeres Menü, kein Discord-Invite → gelbe Hinweise statt stiller Leere

---

## Phase 5 – Neues Admin CP
- [ ] Vollstendige erneuerung kein wenn und aber
- [ ] Wikiseite im acp erstellen
- [ ] Richeditor überall einbinden

## Erfolgskriterien

- Neuer Admin richtet Basis in **&lt; 30 Minuten** mit Checkliste ein  
- Kein zweites Cookie-/Wartungs-/SEO-System  
- Module/Plugins nur über Installer oder klar dokumentiertes Skeleton – keine Geister-Duplikate  
- ACP-Seiten „Module“ und „Plugins“ sind leer verständlich *oder* zeigen installierte Pakete mit Hilfe  
- Mindestens die 7 Kurz-Tutorials aus Phase 1.2 existieren als Markdown  

---

## Dokumente

| Datei | Rolle |
|-------|--------|
| [`ROADMAP.md`](ROADMAP.md) | Diese Planung (lebendig halten) |
| [`MODULE_PLUGIN_LISTE.md`](MODULE_PLUGIN_LISTE.md) | Archiv der gelöschten Altlasten |
| `resources/theme-skeletons/` | Theme-Beispiel (Vorbild für Modul-/Plugin-Skeletons) |

---

*Nächster konkreter Umsetzungsschritt (Empfehlung): Phase 4 Community-Vorlage / Default-Layouts oder Phase 5 ACP.*

# ZerroCMS – Clan CMS

Vollständiges Clan-CMS auf **Laravel** (PHP) mit **Filament** als Admin-Panel. Enthält ein **Theme-System**, **Module** und **Plugins**, Übersetzungen (DE/EN), eigenes Forum, Crafting/Aufträge, UserCP mit Profil, Galerie und weitere Bereiche. Für Shared Hosting (PHP/MySQL) geeignet; Queue läuft als `sync` (kein Redis nötig).

## Projektüberblick

- **Backend:** Laravel 12, Filament 5 (Admin)
- **Frontend:** Blade, Livewire, Tailwind CSS (Vite oder CDN-Fallback)
- **Themes:** Wechselbare Designs (z. B. `default`, `pax-neon`, `pax-cyber`) mit Theme-Editor (Farben, Dark-Mode, Layout)
- **Module:** Ein-/ausschaltbare Funktionsbereiche (z. B. Teamspeak, Discord, Newsletter) mit optionalem `module.json` (Version, Beschreibung) und konfigurierbaren Einstellungen im ACP
- **Plugins:** Erweiterungen (z. B. SEO, Analytics, Maintenance, Donation) mit `plugin.json`, editierbarer Reihenfolge und Konfiguration im ACP
- **Widgets:** Slot-basierte Widget-Instanzen (z. B. Sidebar, Home) mit instanzspezifischer Konfiguration aus dem Widget-`configSchema`
- **Rechte:** Spatie Permission, Rollen und Berechtigungen im Admin unter „Rechteverwaltung“

## Anforderungen

- PHP 8.2+
- Composer
- MySQL/MariaDB
- Empfohlene PHP-Extensions: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Optional: Node.js/npm für Frontend-Assets (Tailwind per Vite; in Produktion empfohlen, sonst CDN-Fallback)

### PHP-Limits für System-Modul-ZIP & lange Imports (Kingshot)

Unter diesen Werten bricht der Vorgang ab. In Plesk → PHP-Einstellungen **mindestens**:

| Einstellung | Minimum |
|-------------|---------|
| `memory_limit` | 1028M |
| `max_execution_time` | 3600 |
| `max_input_time` | 6000 |
| `post_max_size` | 1060M |
| `upload_max_filesize` | 3600M |

Lange Imports laufen im Hintergrund (ACP-Button), weil Proxy/Livewire sonst mit **504** abbricht – unabhängig von `max_execution_time`.

Für `.env` und `APP_KEY` siehe Installer bzw. **plesk.README.md** (Plesk/Shared Hosting).

## Installation

Nur über den **Web-Installer** unter `/install` (keine separaten Setup-Skripte).

1. Document Root auf `public/` zeigen. Einmalig `composer install`, damit Laravel starten kann (oder im Installer-Schritt 1 „Composer installieren“, falls Composer auf dem Server verfügbar ist).
2. Datenbank anlegen (z. B. `CREATE DATABASE zerrocms;`).
3. Im Browser `/install` öffnen und dem Assistenten folgen:

| Schritt | Inhalt |
|--------|--------|
| 1 | Anforderungen prüfen; `.env` + `APP_KEY` automatisch; optional Composer-Abhängigkeiten nachinstallieren |
| 2 | Datenbankverbindung speichern (`QUEUE_CONNECTION=sync`) |
| 3 | Migrationen |
| 4 | Clan-Name, App-URL, Admin-Benutzer – Abschluss führt aus: Seed (Rollen/Menü), `storage:link`, `npm install && npm run build` (falls npm vorhanden) |

Nach Erfolg: Hinweis auf der Startseite und Link zum Admin (`/admin`).

Wenn die Anwendung bereits installiert ist, leitet `/install` automatisch weiter.

### Optional

- **Bewerbungs-Benachrichtigung / Discord:** In `.env`: `APPLICATION_NOTIFY_EMAIL=...`, `DISCORD_WEBHOOK_URL=...`
- **Vite:** Ohne npm nutzt das Theme einen CDN-Fallback für Tailwind.

## Konfiguration

- **Admin-Panel:** `/admin` – Login mit angelegtem User. Dort u. a.: Ränge, Mitglieder, Bewerbungen, Termine, News/Seiten, Widgets (inkl. Instanz-Konfiguration pro Widget), Übersetzungen, Forum, Galerie, Crafting-Aufträge (Craftable Items, Item Requests), Clan-Dokumente, Theme-Editor, Einstellungen (Wartung, Bewerbungen, Frontend), Menüs.
- **Rechteverwaltung:** Unter „Rechteverwaltung“ Rollen und Berechtigungen anlegen/bearbeiten, Benutzern Rollen zuweisen. Rolle **super-admin** hat alle Rechte.
- **Module:** Unter „Module“ im Admin Module ein-/ausschalten; pro Modul optional Version/Beschreibung aus `module.json` und Einstellungen (config.json-Schema) bearbeiten.
- **Plugins:** Unter „Plugins“ Plugins aktivieren/deaktivieren, Reihenfolge festlegen und konfigurieren (plugin.json, inkl. Beschreibung aus Manifest).
- **Theme-Editor:** Aktives Theme, Farben, Dark-Mode (Standard), Layout-Optionen im Admin anpassen.

## Projektstruktur (ausgewählt)

| Bereich        | Beschreibung |
|----------------|--------------|
| `themes/`      | Theme-Ordner (z. B. `default`, `pax-neon`), je mit `theme.json` und `views/` (Layouts, Seiten). |
| `modules/`     | Module mit optionaler `module.json` (name, version, description), `config.json` und `ModuleServiceProvider.php`; Konfiguration im ACP. |
| `plugins/`     | Plugins mit `plugin.json` (inkl. configSchema, description) und Provider; Reihenfolge und Konfiguration im ACP. |
| `app/Support/` | ThemeManager, ModuleManager, PluginManager, Installer. |
| `app/Widgets/` | Widget-Implementierungen mit `configSchema()`; Instanzen unter Filament „Widget-Instanzen“ mit dynamischer Config. |
| `resources/views/usercp/` | UserCP-Views (Profil, Einstellungen, Meine Aufträge). |
| `themes/.../views/crafting/` | Crafting: `index` (Katalog + Tabelle „Meine Aufträge“ + Link „Auftrag erstellen“), `create` (Formular zum Anlegen eines Auftrags). |
| `public/`      | Document Root; `storage` ist Symlink auf `storage/app/public`. |

Uploads (Galerie, Logo, Banner, Avatare) werden über Laravel ausgeliefert (Route `/app-storage/{path}`, Helper `storage_asset()`), damit die URL zur aktuellen Domain (inkl. Subdomain) passt.

- **Status-Check:** `GET /status` liefert JSON mit `ok`, `database`, `installed` (HTTP 200 wenn alles bereit, sonst 503). Für Deployment/Monitoring geeignet.

## Entwicklung

- **Übersetzungen:** `lang/de.json` und `lang/en.json`; Keys z. B. `install.*`, `crafting.*`, `forum.*`. Neue Texte dort anlegen und in Views mit `__('key')` nutzen.
- **Module:** Ordner unter `modules/` mit optionaler `module.json` (name, version, description), optionaler `config.json` (Schema für ACP) und `ModuleServiceProvider.php`; Registrierung über `ModuleManager`, Aktivierung in DB-Tabelle `modules`.
- **Plugins:** Ordner unter `plugins/` mit `plugin.json` (inkl. `configSchema`, `description`); Provider optional; Aktivierung und Reihenfolge in DB-Tabelle `plugins`.
- **Widgets:** `app/Widgets/` und `WidgetRegistry` in `AppServiceProvider`; Widget-Instanzen in Filament unter „Widget-Instanzen“ pro Slot (z. B. `sidebar`, `home`) mit dynamischen Konfigurationsfeldern aus `configSchema()`.

## Hosting & Deployment

- **Document Root:** Immer auf `public/` zeigen (Laravel-Sicherheit).
- **Queue:** `QUEUE_CONNECTION=sync` beibehalten, falls kein Redis/Cron verfügbar ist.
- **Frontend:** In Produktion `npm ci && npm run build`; Dateien in `public/build/` werden vom Frontend genutzt.
- **Nach Updates:** `composer install --no-dev`, `php artisan migrate --force`, `php artisan config:cache`, ggf. `php artisan view:cache`, bei Frontend-Änderungen erneut `npm run build`.

### Plesk / Subdomain

- Ausführliche Anleitung: **plesk.README.md**
- Document Root der (Sub-)Domain auf `.../public` setzen.
- `APP_KEY` setzen (z. B. `php artisan key:generate` oder einmalig `setup-key.php`).
- **ModSecurity:** Bei 403 (z. B. Regel 214620) `APP_DEBUG=false` setzen und ggf. Regel für die Domain anpassen.
- **403 auf /storage:** Symlink `public/storage` prüfen (`php artisan storage:link`); bei Subdomain gleichen Document Root nutzen. In `public/.htaccess` kein `FollowSymLinks`, wenn der Hoster es verbietet – Symlink-Zugriff ggf. per VHost erlauben.
- **422 bei Livewire-Upload:** `config/livewire.php` (payload/temporary_file_upload) und PHP `upload_max_filesize` / `post_max_size` anheben.

## Lizenz

MIT.

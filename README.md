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

Für `.env` und `APP_KEY` siehe Installer bzw. **plesk.README.md** (Plesk/Shared Hosting).

## Installation

### Variante A: Webbrowser-Installer (empfohlen)

1. Abhängigkeiten installieren und Webserver auf `public/` zeigen:
   ```bash
   composer install
   ```

2. Im Browser die Seite aufrufen (z. B. `https://deine-domain.de/install`).  
   Ohne vorhandene `.env` wird diese aus `.env.example` erzeugt und ein App-Key gesetzt.

3. Dem Assistenten folgen:
   - **Schritt 1:** Systemanforderungen prüfen (PHP, Extensions, Schreibrechte).
   - **Schritt 2:** Datenbankverbindung (Host, Port, Datenbankname, Benutzer, Passwort). Die Datenbank muss vorher angelegt sein.
   - **Schritt 3:** Migrationen ausführen (Tabellen anlegen).
   - **Schritt 4:** Admin-Benutzer anlegen (Name, E-Mail, Passwort). Rollen/Berechtigungen werden automatisch angelegt.

4. Nach Abschluss: Speicher-Link für Uploads erstellen (einmalig):
   ```bash
   php artisan storage:link
   ```

5. Für Produktion: Frontend-Assets bauen (entfernt die Tailwind-CDN-Warnung und lädt gebautes CSS):
   ```bash
   npm ci && npm run build
   ```

Wenn die Anwendung bereits installiert ist, leitet `/install` automatisch auf die Startseite weiter.

### Variante B: Manuelle Installation

1. Abhängigkeiten installieren:
   ```bash
   composer install
   ```

2. Umgebung einrichten:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Datenbank in `.env` konfigurieren, dann:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   Admin-User z. B. mit einem SQL-Insert anlegen (siehe `database/` oder Plesk-README).

4. Öffentlichen Speicher-Link erstellen:
   ```bash
   php artisan storage:link
   ```

### Optional

- **Bewerbungs-Benachrichtigung / Discord:** In `.env`: `APPLICATION_NOTIFY_EMAIL=...`, `DISCORD_WEBHOOK_URL=...`
- **Vite:** Ohne `npm run build` nutzt das Theme einen CDN-Fallback für Tailwind; für Produktion wird Build empfohlen.

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

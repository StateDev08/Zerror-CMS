# ZerroCMS auf Plesk deployen

Kurzanleitung zur Einrichtung von ZerroCMS auf einem Server mit **Plesk** (Webhosting-Control-Panel).

## Voraussetzungen

- **Plesk** (z. B. Plesk Obsidian) mit PHP und MySQL/MariaDB
- **PHP** 8.2 oder höher (in Plesk: Domains → PHP-Einstellungen)
- **MySQL** oder **MariaDB** (über Plesk angelegte Datenbank)
- Optional: **SSH-Zugang** oder **Plesk Node.js** nur, wenn du Frontend-Assets (Vite) bauen willst

## Schritte

### 1. Domain und Dokumentenstamm

- In Plesk: **Domains** → deine Domain → **Hosting & DNS** → **Hosting-Einstellungen**.
- **Dokumentenstamm (Document Root)** auf den Ordner **`public`** deines Projekts setzen, z. B.:
  - `/var/www/vhosts/deine-domain.de/httpdocs/public`  
  oder
  - `/httpdocs/public` (wenn dein Projekt unter `httpdocs` liegt).

Wichtig: Es muss immer der Unterordner **`public`** sein, nicht das Projekt-Root (Laravel-Sicherheit).

### 2. PHP-Version

- **Domains** → deine Domain → **PHP-Einstellungen** (oder **PHP-Version**).
- **PHP 8.2** oder **8.3** auswählen.
- Empfohlene Erweiterungen: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`.

### 3. Projekt-Dateien hochladen

- Alle ZerroCMS-Dateien (z. B. per **Git**, **FTP** oder **Plesk File Manager**) in den gewünschten Ordner legen, z. B. `httpdocs` (oder einen Unterordner).
- Der Inhalt von `public/` muss im **Document Root** erreichbar sein (siehe Schritt 1). Entweder:
  - Projekt komplett in `httpdocs` legen und Document Root auf `httpdocs/public` zeigen,  
  - oder Projekt außerhalb von `httpdocs` legen und Document Root per **Symbolischem Link** oder Plesk-Einstellung auf `.../projekt/public` zeigen (je nach Anbieter).

### 4. Datenbank anlegen

- In Plesk: **Databases** → **Add Database**.
- Datenbankname und Benutzer anlegen, Passwort notieren.
- Host ist meist `localhost` (oder der von Plesk angezeigte DB-Server).

### 5. Umgebung (.env) – **nicht manuell nötig**

Der Web-Installer / Preinstall erledigt das automatisch:

1. Domain Document Root auf `…/public` setzen  
2. In Plesk eine MySQL-Datenbank anlegen (Zugangsdaten für Installer-Schritt 2)  
3. Projektdateien hochladen (idealerweise **inkl. `vendor/`**, sonst Composer im Browser)  
4. Im Browser **https://deine-domain.de/** öffnen → Preinstall/Installer  
5. Falls `vendor/` fehlt: Button „Abhängigkeiten installieren“  
6. Installer-Schritte durchklicken (DB, Migrate, Site, Admin)

SSH-Befehle (`composer`, `key:generate`, `migrate`, Admin per tinker) sind **nicht erforderlich**.

### 6. Abhängigkeiten (nur Fallback mit SSH)

Falls SSH verfügbar ist und der Browser-Composer scheitert:

```bash
cd /pfad/zum/projekt
composer install --no-dev --optimize-autoloader
```

Ohne SSH und ohne `proc_open`: Release-ZIP **inklusive `vendor/`** und idealerweise `public/build/` hochladen.

### 7. Admin-Benutzer

Wird im letzten Installer-Schritt angelegt. Danach: **https://deine-domain.de/admin**

### 8. Schreibrechte

- Laravel braucht Schreibrechte auf `storage/` und `bootstrap/cache/` (und Schreibrecht im Projektroot für `.env`).
- Unter Plesk: Domänenbenutzer, typisch 755/644; bei Problemen `storage` und `bootstrap/cache` auf 775.

### 9. Queue (optional)

ZerroCMS ist mit **`QUEUE_CONNECTION=sync`** betriebsbereit (kein Redis/Cron nötig).

## Kurz-Checkliste (ohne SSH)

| Schritt | Aktion |
|--------|--------|
| 1 | Document Root auf `public/` |
| 2 | PHP 8.2+ + Extensions |
| 3 | Dateien hochladen (besser inkl. `vendor/`) |
| 4 | MySQL-Datenbank in Plesk anlegen |
| 5 | Browser: Domain öffnen → Preinstall/Installer |
| 6 | Admin unter `/admin` testen |

## Fehlerbehebung (403 / 500)

### 500-Fehler und „Failed to load resource: 500“ (z. B. clan-members / Roster)

Wenn im Browser **500** oder **„clan-members:1 Failed to load resource: 500“** erscheint und im Apache-Log **ModSecurity: Access denied … id "214620"** / **"scandir found within RESPONSE_BODY"** steht:

1. **Ursache:** Laravel wirft eine Exception (z. B. fehlende Tabelle, fehlende View). Bei **APP_DEBUG=true** sendet Laravel eine HTML-Fehlerseite mit Stacktrace – darin kommt das Wort „scandir“ vor (aus dem PHP-Stacktrace). Die COMODO-WAF blockiert diese Antwort und liefert 403; der Browser kann das als 500 anzeigen.
2. **Sofort:** In **`.env`** **`APP_DEBUG=false`** setzen. Dann sendet Laravel bei Fehlern keine detaillierte Seite mehr, die ModSecurity-Regel 214620 greift nicht, und du siehst entweder eine normale Laravel-Fehlerseite oder die Seite lädt.
3. **Echten Fehler finden:** Auf dem Server in **`storage/logs/laravel.log`** die letzten Einträge lesen (unten in der Datei). Dort steht die echte Exception (z. B. „Table 'clan_members' doesn't exist“ → Migrationen ausführen: `php artisan migrate --force`).
4. **Optional:** In Plesk unter der Domain **ModSecurity** die Regel **214620** für diese Domain deaktivieren, wenn du temporär mit `APP_DEBUG=true` debuggen willst (nur für Entwicklung, nicht für Produktion).

### 403 + „500 while using an ErrorDocument“

Wenn die Meldung lautet: **„Forbidden – You don't have permission to access this resource. Additionally, a 500 Internal Server Error error was encountered while trying to use an ErrorDocument to handle the request.“**

- **Hauptursache:** Der **Dokumentenstamm (Document Root)** zeigt **nicht** auf den Ordner **`public`** (z. B. auf `httpdocs` statt `httpdocs/public`). Dann liefert Apache 403 (kein Zugriff auf das Verzeichnis bzw. keine gültige Index-Datei), und die konfigurierte 403-Fehlerseite schlägt selbst mit 500 fehl.
- **Lösung:** In Plesk unter **Domains** → deine Domain → **Hosting & DNS** → **Hosting-Einstellungen** den **Dokumentenstamm** auf den Ordner setzen, in dem **`index.php`** liegt, also z. B. `.../httpdocs/public` (niemals nur `httpdocs`).
- **Danach:** Falls weiterhin 403: Siehe „403 durch ModSecurity“ unten und `APP_DEBUG=false` setzen.

### 403: Schnellcheck

Wenn **„Failed to load resource: 403“** erscheint, zuerst prüfen **welche URL** betroffen ist (Browser: F12 → Network → fehlgeschlagene Anfrage anklicken → Request URL):

- **Startseite, /admin, /crafting, /install oder andere PHP-Seiten:** Sehr oft **ModSecurity/WAF** – siehe unten „403 durch ModSecurity“. Zusätzlich **Document Root** prüfen (muss auf `.../public` zeigen).
- **Forum-Seiten (/forum/…):** 403 kann gewollt sein (keine Berechtigung). Einloggen oder Rechte in Admin prüfen.
- **Statische Dateien (CSS, JS, Bilder):** Pfad und Schreibrechte prüfen; bei `/storage/…` muss `php artisan storage:link` gelaufen sein. Siehe unten **„403 auf /storage/gallery/… (Bilder)“**.

### 403 auf /storage/gallery/… (Bilder, Galerie, Uploads)

Wenn **`/storage/gallery/…`** oder andere Dateien unter `/storage/` mit **403 Forbidden** antworten, obwohl die URLs korrekt sind (z. B. auf die richtige Domain zeigen):

1. **Symlink prüfen:** `ls -la public/storage` (per SSH) – es muss ein Symlink auf `../storage/app/public` zeigen. Falls nicht: `php artisan storage:link` ausführen.
2. **Subdomain-Document-Root:** Wenn die Seite unter einer Subdomain (z. B. `lichtbringer.drenor.de`) läuft, muss der **Dokumentenstamm dieser Subdomain** auf denselben Ordner zeigen wie die Hauptdomain (also `.../httpdocs/public`). Sonst fehlt der Symlink `public/storage` im Pfad der Subdomain.
3. **Dateirechte:** Verzeichnisse `storage/app/public` und `storage/app/public/gallery` mit **755**, Dateien mit **644**. Der Webserver-Benutzer muss lesen können.
4. **Apache „Option FollowSymLinks not allowed here“:** Wenn im Apache-Log **Option FollowSymLinks not allowed here** für `public/.htaccess` steht, verbietet Plesk diese Option in .htaccess. Im Projekt ist in `public/.htaccess` **kein** `Options +FollowSymLinks` mehr – es werden nur noch Rewrite-Regeln verwendet. Tritt der Fehler trotzdem auf: In einem **übergeordneten** Verzeichnis (z. B. `lichtbringer.drenor.de` oberhalb von `public/`) darf in der `.htaccess` kein `Options +FollowSymLinks` stehen; ggf. diese Zeile dort entfernen. Alternativ den Hoster bitten, für den VHost der Subdomain Symlink-Zugriff im Apache zu erlauben. Uploads werden ohnehin über die Laravel-Route `/app-storage/{path}` ausgeliefert (kein Symlink-Zugriff nötig).
5. **ModSecurity:** Manche WAF-Regeln blockieren Zugriffe auf bestimmte Pfade. In Plesk: ModSecurity/WAF prüfen und ggf. Ausnahme für `/storage/` anlegen.
- **Livewire-Upload (/livewire-…/upload-file):** Typischer ist 422 (Validierung). 403 kann ModSecurity oder WAF-Regeln für POST-Anfragen sein – ggf. Ausnahme für diesen Pfad oder Logo/Banner per klassischem Formular-Upload nutzen (ohne Livewire).

### 500: So findest du die genaue Ursache

Ein **500 Internal Server Error** kann viele Ursachen haben (PHP Fatal, fehlende DB, falsche Typen in Filament-Resources usw.). So siehst du den echten Fehler:

1. **Laravel-Log auf dem Server** (zuverlässigste Quelle):  
   Datei `storage/logs/laravel.log` im Projekt-Root öffnen (per SSH, FTP oder Plesk File Manager). Die **letzten Einträge** unten in der Datei enthalten die Exception inkl. Datei und Zeile.
2. **HTML-Quelltext der 500-Seite**:  
   Im Browser **Rechtsklick → „Seitenquelltext anzeigen“**. Wenn `APP_DEBUG=true` war oder der Server eine Fehlerseite mit Stacktrace ausgibt, steht die Meldung (z. B. „Type of … must be …“) im HTML.  
   **Hinweis:** In Produktion `APP_DEBUG=false` setzen und Fehler nur im Log prüfen (Sicherheit).

Sobald du die genaue Meldung kennst, sieh oben (ModSecurity, PermissionResource, Document Root) bzw. unten (DB, Berechtigungen) nach.

### 500: „Table '…sessions' doesn't exist“

Wenn in der `.env` **SESSION_DRIVER=database** steht, braucht Laravel die Tabelle `sessions` – die gibt es aber erst nach den Migrationen. Die App stellt während der Installation (vor `storage/installed`) den Session-Treiber automatisch auf **file** um. Wenn du den Fehler trotzdem siehst: aktuellen Stand deployen (diese Anpassung ist im Code), oder in `.env` vor dem ersten Aufruf von `/install` vorübergehend **SESSION_DRIVER=file** setzen.

### 500: „No application encryption key“ / MissingAppKeyException

Laravel braucht **APP_KEY** in der `.env` bereits beim ersten Request (z. B. für Sessions). Ohne Key liefert jede Seite (inkl. `/install`) 500.

- **Lösung:** Siehe Schritt 5 oben – Key setzen per `php artisan key:generate` (SSH) oder **einmalig https://deine-domain.de/setup-key.php** aufrufen (erzeugt und speichert APP_KEY), danach `setup-key.php` löschen.

### 500: „Unsupported cipher or incorrect key length“

Wenn im Log **Unsupported cipher or incorrect key length. Supported ciphers are: aes-128-cbc, aes-256-cbc, …** steht, ist **APP_KEY** in der `.env` gesetzt, aber **ungültig** (z. B. abgeschnitten, falsches Format, Leerzeichen/Zeilenumbruch in der Zeile).

- **Lösung:** Auf dem Server im Projekt-Root einen **neuen Key** erzeugen: `php artisan key:generate` (überschreibt APP_KEY in `.env` mit einem gültigen base64-Key). Falls `config:clear` vorher wegen ungültiger .env scheitert: APP_KEY in `.env` vorübergehend **leer** lassen (`APP_KEY=`), dann `php artisan key:generate` ausführen – damit schreibt Laravel den Key korrekt. Alternativ lokal `php artisan key:generate` ausführen und die erzeugte Zeile `APP_KEY=base64:...` (eine Zeile, kein Umbruch) in die Server-.env kopieren.

### 403 durch ModSecurity („scandir found within RESPONSE_BODY“ / „Outbound Points Exceeded“ 214940)

Wenn im Apache-Log **ModSecurity: Access denied … id "214620"** oder **"scandir found within RESPONSE_BODY"** oder **"Outbound Points Exceeded| Total Points: 4"** (id **214940**) steht, blockiert die COMODO-WAF die Antwort: Im HTML steht das Wort „scandir“ (z. B. in der Laravel-Fehlerseite/Stacktrace).

**Sofort-Checkliste (der Reihe nach):**

1. **`.env` auf dem Server prüfen**  
   Im Projekt-Root (z. B. `/var/www/vhosts/drenor.de/lichtbringer.drenor.de/`) in der Datei **`.env`** steht:
   ```env
   APP_DEBUG=false
   ```
   Kein Leerzeichen, kein `true`. Danach **Config-Cache leeren** (per SSH):  
   `php artisan config:clear`  
   Ohne SSH: `.env` speichern und einen Moment warten, dann Seite neu laden.

2. **Echten Fehler ermitteln**  
   Wenn die Seite trotzdem 403 bleibt oder eine leere/generische Fehlerseite zeigt: In **`storage/logs/laravel.log`** die letzten Zeilen lesen. Dort steht die eigentliche Exception (z. B. fehlende Tabelle, fehlende View). Diese Ursache beheben – dann liefert Laravel wieder normales HTML ohne Stacktrace, und ModSecurity schlägt nicht mehr an.

3. **Falls 403 weiterhin auftritt: ModSecurity-Regel für die Domain ausschalten**  
   In Plesk: **Domains** → **lichtbringer.drenor.de** → **Web Application Firewall (WAF)** bzw. **ModSecurity** oder **Sicherheit**. Dort die Regel **214620** („PHP source code leakage“) für diese Domain deaktivieren oder eine Ausnahme anlegen. Optional auch **214940** (Outbound Points Exceeded) deaktivieren, falls sie weiter blockiert.  
   *Hinweis:* Die Regel schützt vor dem Anzeigen von PHP-Code in Fehlerseiten. Mit `APP_DEBUG=false` und behobenen Fehlern sollte sie im Betrieb nicht mehr greifen.

### 500 / PHP Fatal (PermissionResource, navigationGroup, navigationIcon)

Wenn im Log **Type of … $navigationGroup must be UnitEnum|string|null** oder **$navigationIcon must be BackedEnum|string|null** steht: Auf dem Server liegt noch alter Code. Dann schlagen **alle** Aufrufe mit 500 fehl – auch **`/install`**, weil Laravel und Filament bei jedem Request starten und die fehlerhaften Resource-Klassen schon beim Boot laden.

- **Lösung:** Projekt **komplett neu deployen** (alle Dateien aus dem Repo, inkl. der angepassten `app/Filament/Resources/PermissionResource.php`, `RoleResource.php`, `UserResource.php`), danach z. B. `php artisan config:clear`.
- **Nur FTP/Dateimanager:** Die drei genannten Dateien aus dem Repo in `app/Filament/Resources/` auf dem Server ersetzen – danach sollten Startseite und `/install` wieder antworten.

### „Primary script unknown“ / Verzeichnis wird gelistet

- **Document Root** muss exakt auf den Ordner zeigen, in dem **`index.php`** liegt, also z. B. `/var/www/vhosts/drenor.de/httpdocs/public` (nicht `httpdocs`).
- In Plesk: **Domains** → Domain → **Hosting & DNS** → **Hosting-Einstellungen** → **Dokumentenstamm** = `.../httpdocs/public` (bzw. der Pfad, der mit `/public` endet).

### Verzeichnis „Cannot serve directory …/httpdocs/“

Das bedeutet: Es wird das Projekt-Root statt `public` als Document Root verwendet. Document Root wie oben auf `.../httpdocs/public` setzen.

### 422 beim Livewire-Datei-Upload (Logo/Banner o. Ä.)

Ein **POST …/livewire-…/upload-file** mit **422 Unprocessable Content** bedeutet: Die Upload-Validierung ist fehlgeschlagen (z. B. Datei zu groß, kein Bild oder PHP-Limit).

- **Livewire:** In `config/livewire.php` unter `temporary_file_upload.rules` sind aktuell nur Bilder bis 5 MB erlaubt (`image`, `max:5120` KB).
- **PHP-Limits:** `upload_max_filesize` und `post_max_size` müssen mindestens so groß sein (z. B. 8 MB). In Plesk: **PHP-Einstellungen** der Domain → z. B. `upload_max_filesize = 8M`, `post_max_size = 10M`.
- **Schreibrechte:** Das temporäre Upload-Verzeichnis (Standard: unter `storage/app`) muss für den Webserver beschreibbar sein.

### SSL-Warnung: „server certificate does NOT include an ID which matches the server name“

Wenn im Log **AH01909: … :443:0 server certificate does NOT include an ID which matches the server name** steht (z. B. für **www.lichtbringer.drenor.de**), gilt das Zertifikat nur für die Domain ohne **www** (z. B. **lichtbringer.drenor.de**). Entweder: Zertifikat in Plesk so erweitern, dass **www.** mit abgedeckt ist (SAN), oder Besucher nur **lichtbringer.drenor.de** (ohne www) nutzen und www per Weiterleitung auf die nicht-www-Version leiten.

### Log-Einträge zu /.env, /.git, /trace.axd

Einträge wie **ModSecurity: … Matched phrase "/.env"** oder **"/.git/"** oder **".axd"** sind Abfragen von Bots/Skannern. ModSecurity blockiert sie mit 403 – das ist gewollt. Keine Aktion nötig.

## Weitere Infos

- Allgemeine Installation und Nutzung: siehe **README.md**.
- Shared-Hosting-Hinweise (ohne Plesk): ebenfalls in **README.md** (Abschnitt „Hosting“).

# ZerroCMS Example Theme

Dieses Paket ist ein **Starter-Theme**. Du kannst es lokal bearbeiten und als ZIP im Admin unter **System → Themes** hochladen.

## Ordnerstruktur

```
example/
  theme.json          # Manifest (name, Farben, Label) – Pflicht
  THEME.md            # Diese Datei
  views/
    layouts/app.blade.php
    home.blade.php
    partials/
      top-nav.blade.php
      theme-mode-boot.blade.php
      external-links.blade.php
      site-banner.blade.php   # optional – sonst greift common
  public/             # optional: CSS/JS/Bilder → /themes/example/...
```

## Regeln

1. **`name` in theme.json** = Ordner-ID nach Installation (nur `a-z`, `0-9`, `-`, z. B. `my-clan`).
2. Nicht reserviert: `common` ist gesperrt.
3. Views, die du **nicht** überschreibst, kommen automatisch aus `themes/common/` (News, Forum, Wiki, …).
4. CSS-Variablen: `--theme-primary`, `--theme-accent`, `--theme-bg`, `--theme-surface`, `--theme-text`, `--theme-muted`.
5. Startseite: In der Mitte nur Banner/Slider via `@include('theme::partials.hero-media')` – **kein** Emblem-/CTA-Kasten.
6. Top-Menü: In `top-nav` die Links via `@include('theme::partials.top-nav-links')` – editierbar unter **System → Menüeinträge** (Position **Oben**).
7. Startseiten-Inhalt: `@include('theme::partials.home-content')` – Willkommenstext aus ACP, Widgets über Slots `home`/`sidebar`.

## ZIP bauen

Ordner so zippen, dass **entweder**

- `theme.json` im ZIP-Root liegt, **oder**
- genau ein Unterordner `dein-name/` mit `theme.json` drin liegt.

Dann im ACP hochladen. Bei gleichem Namen Häkchen „Überschreiben“ setzen.

## Tipps

- Body-Klasse: `theme-example` (an `name` anpassen).
- Fonts: Google Fonts oder Dateien unter `public/fonts/`.
- Nach dem Aktivieren: **Theme-Editor** für Farben feinjustieren.

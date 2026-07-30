# BlueByte Development Theme

Professionelles ZerroCMS-Theme im **Icy Techno-Fantasy**-Stil (Mitternachtblau, Cyan-Glow, Glasflächen).

## Installation

1. Diese ZIP im ACP unter **System → Themes** hochladen
2. Theme **BlueByte Development** aktivieren
3. Optional Farben unter **Theme-Editor** feinjustieren
4. Eigenes Banner/Logo unter **Einstellungen** setzen

## Anpassen

- `theme.json` → `name`, `label`, Farben
- `views/layouts/app.blade.php` → Globales CSS / Frame
- `views/home.blade.php` → Hero & Startseite
- `views/partials/top-nav.blade.php` → Navigation

Alle anderen Seiten (News, Forum, Wiki, …) kommen aus `themes/common/`.

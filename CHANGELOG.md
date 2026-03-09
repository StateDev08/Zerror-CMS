# Changelog / Funktionsübersicht ZerroCMS

Alle in der Dokumentation (README, dokumentations.html) genannten Funktionen und wo sie im ACP bzw. Frontend zu finden sind.

## Admin-Panel (ACP) unter `/admin`

Nach dem Login erscheinen die Bereiche in der linken Navigation, gruppiert wie folgt:

| Gruppe | Enthaltene Ressourcen/Seiten |
|--------|-----------------------------|
| **Übersicht** | Dashboard |
| **Clan** | Ränge, Mitglieder, Clan Bewerbung, Craftable Items, Item-Aufträge, Clan Teams, Team-Mitglieder, Clan Regeln, Clan Bank (Kategorien, Bank-Items), Clan Kasse (Kategorien, Kassenbuch), Clan Dokumente (Kategorien, Dokumente), Clan Rangliste (Kategorien, Einträge), Clan Feedback, Clan Ankündigungen, Clan Erfolge |
| **Galerie** | Alben, Bilder |
| **Downloads** | Kategorien, Dateien |
| **Game Marketplace** | Kategorien, Anzeigen |
| **Stellenangebote** | Kategorien, Stellenangebote |
| **Inhalte** | News & Seiten, Slider, Termine, Partner, Umfragen, Newsletter-Abonnenten, Medien, Widget-Instanzen |
| **Forum** | Forum-Kategorien, Forum-Foren |
| **Wiki** | Wiki-Kategorien, Wiki-Artikel |
| **Rechteverwaltung** | Benutzer, Rollen, Berechtigungen |
| **System** | Einstellungen, Themes, Theme-Editor, Module, Plugins, Menüeinträge, Übersetzungen, Benachrichtigungen |

## Frontend-Routen (öffentlich bzw. nach Login)

- `/` – Startseite (Home)
- `/news` – News-Übersicht
- `/news/{slug}` – Einzelnews
- `/roster` – Mitglieder/Roster
- `/calendar` – Termine
- `/apply` – Bewerbung (wenn aktiviert)
- `/page/{slug}` – statische Seiten (z. B. Impressum, Datenschutz)
- `/gallery` – Galerie
- `/downloads` – Downloads
- `/partners` – Partner
- `/polls` – Umfragen
- `/newsletter` – Newsletter-Anmeldung
- `/wiki` – Wiki
- `/marketplace` – Game Marketplace
- `/jobs` – Stellenangebote
- `/forum` – Forum (Foren, Threads, Beiträge)
- `/crafting` – Crafting/Aufträge
- `/clan/teams`, `/clan/bank`, `/clan/rules`, `/clan/leaderboard`, `/clan/documents`, `/clan/feedback`, `/clan/announcements`, `/clan/achievements` – Clan-Bereiche
- `/user/{user}` – öffentliches Profil
- `/usercp` – User Control Panel (Profil, Passwort, Meine Aufträge)

## Hinweis

Falls Einträge im ACP fehlen: Cache leeren (`php artisan config:clear`, `php artisan view:clear`) und erneut einloggen. Alle Filament-Resources haben eine `navigationGroup`-Zuweisung, damit sie in der passenden Gruppe erscheinen.

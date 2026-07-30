# System-Modul: example

Eigenständiger Paket-Typ unter `system-modules/` (nicht `modules/` / `plugins/`).

## Pflicht

- `system-module.json` – id, name, version, description
- `SystemModuleServiceProvider.php` – gibt Provider-Klasse per `return` zurück

## v1-Vertrag

Der Provider darf:

- Views laden (`loadViewsFrom`)
- Routes registrieren
- Config / Settings nutzen
- ACP-Aktionen über `SystemModuleManager::registerAdminAction($id, …)` an die System-Module-Karte hängen

Filament-ACP-Seiten aus dem Paket: später (Phase 2).

## Installation

ZIP über ACP → System → Paket-Installer (Typ System-Modul) oder Ordner nach `system-modules/{id}/` kopieren und in „System-Module“ aktivieren.

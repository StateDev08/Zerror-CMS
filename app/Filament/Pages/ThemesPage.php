<?php

namespace App\Filament\Pages;

use App\Support\ThemeManager;
use Filament\Pages\Page;

class ThemesPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Themes';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Theme auswählen';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.themes';

    public function getViewData(): array
    {
        $manager = app(ThemeManager::class);
        return [
            'themes' => $manager->discover(),
            'active' => $manager->active(),
        ];
    }

    public function setTheme(string $name): void
    {
        $manager = app(ThemeManager::class);
        $themes = $manager->discover();
        if (! isset($themes[$name])) {
            return;
        }
        $manager->setActive($name);
        $this->dispatch('refresh-page');
    }
}

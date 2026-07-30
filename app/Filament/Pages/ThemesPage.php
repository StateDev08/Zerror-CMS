<?php

namespace App\Filament\Pages;

use App\Support\ThemeInstaller;
use App\Support\ThemeManager;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ThemesPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Themes';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Themes';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.themes';

    protected static function cmsPagePermission(): string
    {
        return 'manage_themes';
    }

    public function getViewData(): array
    {
        $manager = app(ThemeManager::class);
        $installer = app(ThemeInstaller::class);
        $themes = $manager->discover(selectableOnly: true);
        $meta = [];
        foreach ($themes as $key => $theme) {
            $name = $theme['name'] ?? $key;
            $meta[$name] = [
                'builtin' => $installer->isBuiltin($name),
            ];
        }

        return [
            'themes' => $themes,
            'active' => $manager->active(),
            'themeMeta' => $meta,
            'installerUrl' => PackagesInstallerPage::getUrl(),
        ];
    }

    public function setTheme(string $name): void
    {
        $manager = app(ThemeManager::class);
        $themes = $manager->discover(selectableOnly: true);
        if (! isset($themes[$name])) {
            return;
        }
        $manager->setActive($name);
        foreach (($themes[$name]['colors'] ?? []) as $key => $value) {
            if (is_string($key) && is_string($value) && $value !== '') {
                $manager->setThemeColor($key, $value);
            }
        }

        Notification::make()
            ->title(__('zerrocms.themes.activated', ['name' => $name]))
            ->success()
            ->send();
    }

    public function deleteCustomTheme(string $name): void
    {
        $result = app(ThemeInstaller::class)->deleteTheme($name);
        if (! $result['ok']) {
            Notification::make()
                ->title(__('zerrocms.themes.delete_failed'))
                ->body($result['message'])
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('zerrocms.themes.delete_ok'))
            ->body($result['message'])
            ->success()
            ->send();
    }
}

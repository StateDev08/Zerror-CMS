<?php

namespace App\Filament\Pages;

use App\Support\ThemeManager;
use Filament\Pages\Page;

class ThemeColorsPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-swatch';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Theme-Editor';

    protected static ?string $title = 'Theme-Editor';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.theme-colors';

    public string $primary = '#3b82f6';

    public string $accent = '#10b981';

    public string $background = '#f9fafb';

    public string $surface = '#ffffff';

    public string $text = '#111827';

    public string $text_muted = '#6b7280';

    public string $default_theme_mode = 'system';

    public string $nav_sidebar_position = 'left';

    public string $widget_sidebar_position = 'right';

    public string $main_order = 'content_first';

    public function mount(): void
    {
        $manager = app(ThemeManager::class);
        $colors = $manager->getThemeColors();
        $this->primary = $colors['primary'];
        $this->accent = $colors['accent'];
        $this->background = $colors['background'];
        $this->surface = $colors['surface'];
        $this->text = $colors['text'];
        $this->text_muted = $colors['text_muted'];
        $this->default_theme_mode = $manager->getDefaultThemeMode();
        $layout = $manager->getLayoutOptions();
        $this->nav_sidebar_position = $layout['nav_sidebar_position'];
        $this->widget_sidebar_position = $layout['widget_sidebar_position'];
        $this->main_order = $layout['main_order'];
    }

    public function save(): void
    {
        $hexRule = ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'];
        $this->validate([
            'primary' => $hexRule,
            'accent' => $hexRule,
            'background' => $hexRule,
            'surface' => $hexRule,
            'text' => $hexRule,
            'text_muted' => $hexRule,
            'default_theme_mode' => ['required', 'in:light,dark,system'],
            'nav_sidebar_position' => ['required', 'in:left,right'],
            'widget_sidebar_position' => ['required', 'in:left,right'],
            'main_order' => ['required', 'in:content_first,widgets_first'],
        ]);

        $manager = app(ThemeManager::class);
        $manager->setThemeColor('primary', $this->primary);
        $manager->setThemeColor('accent', $this->accent);
        $manager->setThemeColor('background', $this->background);
        $manager->setThemeColor('surface', $this->surface);
        $manager->setThemeColor('text', $this->text);
        $manager->setThemeColor('text_muted', $this->text_muted);
        $manager->setDefaultThemeMode($this->default_theme_mode);
        $manager->setLayoutOption('nav_sidebar_position', $this->nav_sidebar_position);
        $manager->setLayoutOption('widget_sidebar_position', $this->widget_sidebar_position);
        $manager->setLayoutOption('main_order', $this->main_order);

        $this->dispatch('refresh-page');
    }
}

<?php

namespace App\Filament\Pages;

use App\Support\ThemeManager;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ThemeColorsPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-swatch';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Theme-Editor';

    protected static ?string $title = 'Theme-Editor';
    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.theme-colors';

    protected static function cmsPagePermission(): string
    {
        return 'manage_theme_editor';
    }

    public string $primary = '#c9a227';

    public string $accent = '#3b82f6';

    public string $background = '#0a0a0c';

    public string $surface = '#141418';

    public string $text = '#f5f5f4';

    public string $text_muted = '#a8a29e';

    public string $default_theme_mode = 'dark';

    public string $nav_sidebar_position = 'left';

    public string $widget_sidebar_position = 'right';

    public string $main_order = 'content_first';

    public string $font_display = 'Fraunces';

    public string $font_body = 'Source Sans 3';

    public string $font_url = '';

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
        $fonts = $manager->getThemeFonts();
        $this->font_display = $fonts['display'];
        $this->font_body = $fonts['body'];
        $this->font_url = $fonts['url'];
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
            'font_display' => ['required', 'string', 'max:120'],
            'font_body' => ['required', 'string', 'max:120'],
            'font_url' => ['nullable', 'string', 'max:500'],
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
        $manager->setThemeFont('display', trim($this->font_display));
        $manager->setThemeFont('body', trim($this->font_body));
        $manager->setThemeFont('url', trim($this->font_url));

        Notification::make()
            ->title(__('zerrocms.theme_editor.saved'))
            ->success()
            ->send();

        $this->dispatch('refresh-page');
    }
}

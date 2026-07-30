@php
    use App\Filament\Pages\ModulesPage;
    use App\Filament\Pages\PackagesInstallerPage;
    use App\Filament\Pages\PluginsPage;
    use App\Filament\Pages\SiteSettingsPage;
    use App\Filament\Pages\SystemModulesPage;
    use App\Filament\Pages\ThemeColorsPage;
    use App\Filament\Pages\ThemesPage;
    use App\Filament\Pages\UpdateDatabasePage;
    use App\Filament\Resources\MenuItemResource;
    use App\Filament\Resources\TranslationResource;
    use App\Filament\Resources\UserNotificationResource;

    $systemLinks = [];

    $candidates = [
        [SiteSettingsPage::class, 'Einstellungen'],
        [PackagesInstallerPage::class, 'Paket-Installer'],
        [ThemesPage::class, 'Themes'],
        [ThemeColorsPage::class, 'Theme-Editor'],
        [ModulesPage::class, 'Module'],
        [PluginsPage::class, 'Plugins'],
        [SystemModulesPage::class, 'System-Module'],
        [UpdateDatabasePage::class, 'Datenbank aktualisieren'],
        [TranslationResource::class, 'Übersetzungen', true],
        [MenuItemResource::class, 'Menüeinträge', true],
        [UserNotificationResource::class, 'Benachrichtigungen', true],
    ];

    foreach ($candidates as $entry) {
        $class = $entry[0];
        $label = $entry[1];
        $isResource = $entry[2] ?? false;

        if (! class_exists($class)) {
            continue;
        }

        try {
            if (method_exists($class, 'canAccess') && ! $class::canAccess()) {
                continue;
            }
            if ($isResource && method_exists($class, 'canViewAny') && ! $class::canViewAny()) {
                continue;
            }

            $url = $isResource ? $class::getUrl('index') : $class::getUrl();
            $systemLinks[] = ['label' => $label, 'url' => $url];
        } catch (\Throwable) {
            continue;
        }
    }
@endphp

@if(count($systemLinks))
    <div class="zc-system-menu" x-data="{ open: false }" @keydown.escape.window="open = false">
        <button
            type="button"
            class="zc-system-menu__btn"
            @click="open = !open"
            :aria-expanded="open.toString()"
            aria-haspopup="true"
            title="System"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.174.1.33.229.464.38.21.234.315.54.275.85l-.247 1.227c-.08.4.04.82.33 1.096l.928.886c.41.39.41 1.03 0 1.42l-.928.886a1.125 1.125 0 0 0-.33 1.096l.247 1.227c.04.31-.066.616-.274.85a2.3 2.3 0 0 1-.465.38c-.332.183-.582.495-.644.87l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a2.25 2.25 0 0 1-.465-.38 1.125 1.125 0 0 1-.275-.85l.247-1.227c.08-.4-.04-.82-.33-1.096l-.928-.886a1.006 1.006 0 0 1 0-1.42l.928-.886c.29-.276.41-.696.33-1.096L8.66 6.04a1.125 1.125 0 0 1 .275-.85 2.3 2.3 0 0 1 .465-.38c.332-.183.582-.495.644-.87l.213-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span class="zc-system-menu__label">System</span>
        </button>

        <div
            class="zc-system-menu__panel"
            x-show="open"
            x-cloak
            x-transition.opacity.duration.150ms
            @click.outside="open = false"
            role="menu"
        >
            <p class="zc-system-menu__heading">System</p>
            @foreach($systemLinks as $link)
                <a href="{{ $link['url'] }}" class="zc-system-menu__link" role="menuitem" @click="open = false">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif

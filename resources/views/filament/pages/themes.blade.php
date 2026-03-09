<x-filament-panels::page>
    <p class="text-gray-500 dark:text-gray-400 mb-4">Aktives Theme: <strong>{{ $active }}</strong>. Gefundene Themes aus dem Ordner <code>themes/</code>.</p>
    @if(empty($themes))
        <p class="text-gray-500 dark:text-gray-400">Keine Themes gefunden. Lege einen Ordner mit <code>theme.json</code> unter <code>themes/</code> an.</p>
    @else
        <ul class="space-y-3">
            @foreach($themes as $key => $theme)
                @php
                    $themeName = $theme['name'] ?? $key;
                @endphp
                <li class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div>
                        <span class="font-medium">{{ $themeName }}</span>
                        @if(!empty($theme['version']))
                            <span class="text-sm text-gray-500 dark:text-gray-400">v{{ $theme['version'] }}</span>
                        @endif
                        @if(($active ?? '') === $themeName)
                            <span class="text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 px-2 py-0.5 rounded ml-2">Aktiv</span>
                        @endif
                    </div>
                    @if(($active ?? '') !== $themeName)
                        <x-filament::button
                            wire:click="setTheme('{{ str_replace(['\\', chr(39)], ['\\\\', '\\' . chr(39)], $themeName) }}')"
                            color="primary"
                            size="sm"
                        >
                            Als Theme wählen
                        </x-filament::button>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-filament-panels::page>

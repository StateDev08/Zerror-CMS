<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Farben</x-slot>
            <x-slot name="description">Theme-Farben für das Frontend (Hex, z. B. #3b82f6).</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Primärfarbe</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="primary" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="primary" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#3b82f6" maxlength="7">
                    </div>
                    @error('primary') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Akzentfarbe</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="accent" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="accent" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#10b981" maxlength="7">
                    </div>
                    @error('accent') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Hintergrund</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="background" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="background" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#f9fafb" maxlength="7">
                    </div>
                    @error('background') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Flächenfarbe (Surface)</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="surface" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="surface" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#ffffff" maxlength="7">
                    </div>
                    @error('surface') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Textfarbe</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="text" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="text" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#111827" maxlength="7">
                    </div>
                    @error('text') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Gedämpfte Textfarbe</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="color" wire:model="text_muted" class="h-12 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
                        <input type="text" wire:model="text_muted" class="filament-input block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm" placeholder="#6b7280" maxlength="7">
                    </div>
                    @error('text_muted') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Dark-Mode</x-slot>
            <x-slot name="description">Standard-Anzeige für Besucher ohne gespeicherte Präferenz (Cookie).</x-slot>
            <div>
                <label class="block text-sm font-medium mb-1">Standard-Anzeige</label>
                <select wire:model="default_theme_mode" class="filament-input block w-64 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                    <option value="system">System (Browser-Einstellung)</option>
                    <option value="light">Hell</option>
                    <option value="dark">Dunkel</option>
                </select>
                @error('default_theme_mode') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Layout</x-slot>
            <x-slot name="description">Position der Bereiche: Nav-Sidebar, Widget-Sidebar und Reihenfolge im Hauptbereich.</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Nav-Sidebar (Menü)</label>
                    <select wire:model="nav_sidebar_position" class="filament-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                        <option value="left">Links</option>
                        <option value="right">Rechts</option>
                    </select>
                    @error('nav_sidebar_position') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Widget-Sidebar</label>
                    <select wire:model="widget_sidebar_position" class="filament-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                        <option value="left">Links</option>
                        <option value="right">Rechts</option>
                    </select>
                    @error('widget_sidebar_position') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Reihenfolge im Hauptbereich</label>
                    <select wire:model="main_order" class="filament-input block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                        <option value="content_first">Inhalt zuerst, dann Widgets</option>
                        <option value="widgets_first">Widgets zuerst, dann Inhalt</option>
                    </select>
                    @error('main_order') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-filament::section>

        <x-filament::button type="submit" wire:loading.attr="disabled">
            Speichern
        </x-filament::button>
    </form>
</x-filament-panels::page>

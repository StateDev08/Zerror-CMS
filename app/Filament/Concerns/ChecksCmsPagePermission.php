<?php

namespace App\Filament\Concerns;

/**
 * Custom Filament-Pages (Einstellungen, Module, …) über benannte Permissions absichern.
 */
trait ChecksCmsPagePermission
{
    /**
     * z. B. manage_settings, manage_modules, manage_themes
     */
    protected static function cmsPagePermission(): string
    {
        return 'access_admin';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $perm = static::cmsPagePermission();

        return $user->hasPermissionTo('access_admin')
            && ($perm === 'access_admin' || $user->hasPermissionTo($perm));
    }
}

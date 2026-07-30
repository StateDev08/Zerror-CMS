<?php

namespace App\Filament\Concerns;

use App\Support\PermissionHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Filament-Ressourcen an Spatie-Permissions koppeln (view_any posts, …).
 */
trait ChecksCmsPermissions
{
    public static function canViewAny(): bool
    {
        return static::cmsUserCan('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::cmsUserCan('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::cmsUserCan('update', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::cmsUserCan('delete', $record);
    }

    public static function canDeleteAny(): bool
    {
        return static::cmsUserCan('deleteAny');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::cmsUserCan('forceDelete', $record);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::cmsUserCan('forceDeleteAny');
    }

    public static function canReorder(): bool
    {
        return static::cmsUserCan('reorder');
    }

    public static function canReplicate(Model $record): bool
    {
        return static::cmsUserCan('replicate', $record);
    }

    public static function canRestore(Model $record): bool
    {
        return static::cmsUserCan('restore', $record);
    }

    public static function canRestoreAny(): bool
    {
        return static::cmsUserCan('restoreAny');
    }

    protected static function cmsUserCan(string $ability, ?Model $record = null): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('super-admin')) {
            return true;
        }

        $model = $record ?? static::getModel();
        $perm = PermissionHelper::abilityToPermissionName($ability, $model);
        if (! $perm) {
            return false;
        }

        return $user->hasPermissionTo($perm);
    }
}

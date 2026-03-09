<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class PermissionHelper
{
    /**
     * Ability-Namen von Filament auf Permission-Name mappen (z. B. "create" + Post -> "create posts").
     * @param  Model|class-string<Model>|null  $model
     */
    public static function abilityToPermissionName(string $ability, Model|string|null $model = null): ?string
    {
        if ($ability === 'access_admin') {
            return 'access_admin';
        }

        if ($model === null) {
            return null;
        }

        $table = is_string($model)
            ? (new $model)->getTable()
            : $model->getTable();
        $table = str_replace('_', ' ', $table);
        $map = [
            'viewAny' => 'view_any',
            'view' => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
            'deleteAny' => 'delete_any',
            'restore' => 'restore',
            'restoreAny' => 'restore_any',
            'replicate' => 'replicate',
            'reorder' => 'reorder',
            'forceDelete' => 'force_delete',
            'forceDeleteAny' => 'force_delete_any',
        ];

        $prefix = $map[$ability] ?? str_replace(['-', ' '], '_', $ability);
        return $prefix . ' ' . $table;
    }
}

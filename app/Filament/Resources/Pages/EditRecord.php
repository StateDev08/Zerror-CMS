<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord as BaseEditRecord;

/**
 * Standard-Edit-Seite: Löschen-Button im Header für alle CMS-Resources.
 */
class EditRecord extends BaseEditRecord
{
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

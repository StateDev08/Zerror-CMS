<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords as BaseListRecords;

abstract class ListRecords extends BaseListRecords
{
    /** @return array<\Filament\Actions\Action|\Filament\Actions\ActionGroup> Instanzen, keine ::class (sonst "Call to a member function table() on string") */
    protected function getTableHeaderActions(): array
    {
        $resource = static::getResource();
        if ($resource::hasPage('create')) {
            return [ CreateAction::make() ];
        }
        return [];
    }
}

<?php

namespace App\Filament\Resources\CraftableItemResource\Pages;

use App\Filament\Resources\CraftableItemResource;
use Filament\Resources\Pages\ListRecords as BaseListRecords;

class ListCraftableItems extends BaseListRecords
{
    protected static string $resource = CraftableItemResource::class;
}

<?php

namespace App\Filament\Resources\ItemRequestResource\Pages;

use App\Filament\Resources\ItemRequestResource;
use Filament\Resources\Pages\ListRecords as BaseListRecords;

class ListItemRequests extends BaseListRecords
{
    protected static string $resource = ItemRequestResource::class;
}

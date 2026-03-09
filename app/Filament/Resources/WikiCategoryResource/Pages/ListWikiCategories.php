<?php

namespace App\Filament\Resources\WikiCategoryResource\Pages;

use App\Filament\Resources\WikiCategoryResource;
use App\Filament\Resources\Pages\ListRecords;

class ListWikiCategories extends ListRecords
{
    protected static string $resource = WikiCategoryResource::class;
}

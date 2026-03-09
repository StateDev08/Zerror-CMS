<?php

namespace App\Filament\Resources\WikiArticleResource\Pages;

use App\Filament\Resources\WikiArticleResource;
use App\Filament\Resources\Pages\ListRecords;

class ListWikiArticles extends ListRecords
{
    protected static string $resource = WikiArticleResource::class;
}

<?php

namespace App\Filament\Resources\WikiArticleResource\Pages;

use App\Filament\Resources\WikiArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWikiArticle extends CreateRecord
{
    protected static string $resource = WikiArticleResource::class;
}

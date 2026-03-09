<?php

namespace App\Filament\Resources\WikiArticleResource\Pages;

use App\Filament\Resources\WikiArticleResource;
use Filament\Resources\Pages\EditRecord;

class EditWikiArticle extends EditRecord
{
    protected static string $resource = WikiArticleResource::class;
}

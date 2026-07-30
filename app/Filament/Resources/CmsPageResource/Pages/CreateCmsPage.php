<?php

namespace App\Filament\Resources\CmsPageResource\Pages;

use App\Filament\Resources\CmsPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCmsPage extends CreateRecord
{
    protected static string $resource = CmsPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id']) && auth()->id()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}

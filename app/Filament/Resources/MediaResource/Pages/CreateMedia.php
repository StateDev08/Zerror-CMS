<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $path = $data['path'] ?? null;
        if (is_array($path)) {
            $path = $path[0] ?? null;
        }
        if ($path) {
            $data['filename'] = basename($path);
            $data['mime_type'] = Storage::mimeType($path);
            $data['size'] = Storage::size($path);
        }
        return $data;
    }
}

<?php

namespace App\Filament\Resources\DiscordQuickCommandResource\Pages;

use App\Filament\Resources\DiscordQuickCommandResource;
use App\Models\DiscordQuickCommand;
use Filament\Resources\Pages\CreateRecord;

class CreateDiscordQuickCommand extends CreateRecord
{
    protected static string $resource = DiscordQuickCommandResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['name'])) {
            $data['name'] = DiscordQuickCommand::normalizeName($data['name']);
        }
        $data['created_by'] = $data['created_by'] ?? auth()->id();
        return $data;
    }
}

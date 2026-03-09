<?php

namespace App\Filament\Resources\DiscordQuickCommandResource\Pages;

use App\Filament\Resources\DiscordQuickCommandResource;
use App\Models\DiscordQuickCommand;
use Filament\Resources\Pages\EditRecord;

class EditDiscordQuickCommand extends EditRecord
{
    protected static string $resource = DiscordQuickCommandResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['name'])) {
            $data['name'] = DiscordQuickCommand::normalizeName($data['name']);
        }
        return $data;
    }
}

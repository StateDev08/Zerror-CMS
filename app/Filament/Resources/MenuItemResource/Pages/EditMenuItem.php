<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $raw = $this->form->getRawState();
        if (! empty($raw['use_custom_link']) && filled($raw['custom_link'] ?? null)) {
            $data['link'] = trim((string) $raw['custom_link']);
        }
        unset($data['use_custom_link'], $data['custom_link']);

        return $data;
    }
}

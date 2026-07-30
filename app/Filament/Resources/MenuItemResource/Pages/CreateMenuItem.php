<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeLinkData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeLinkData(array $data): array
    {
        $raw = $this->form->getRawState();
        if (! empty($raw['use_custom_link']) && filled($raw['custom_link'] ?? null)) {
            $data['link'] = trim((string) $raw['custom_link']);
        }
        unset($data['use_custom_link'], $data['custom_link']);

        return $data;
    }
}

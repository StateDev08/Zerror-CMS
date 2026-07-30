<?php

namespace App\Filament\Resources\WidgetInstanceResource\Pages;

use App\Filament\Resources\WidgetInstanceResource;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pages\EditRecord;

class EditWidgetInstance extends EditRecord
{
    protected static string $resource = WidgetInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

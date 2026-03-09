<?php

namespace App\Filament\Resources;

use App\Models\Application;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Clan Bewerbung';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->disabled(),
            TextInput::make('email')->email()->required()->disabled(),
            Textarea::make('message')->required()->disabled(),
            Select::make('status')->options([
                'open' => 'Offen',
                'accepted' => 'Angenommen',
                'rejected' => 'Abgelehnt',
            ])->required(),
            Textarea::make('notes')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApplicationResource\Pages\ListApplications::route('/'),
            'edit' => \App\Filament\Resources\ApplicationResource\Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}

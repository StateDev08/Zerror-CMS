<?php

namespace App\Filament\Resources;

use App\Models\ClanTreasuryCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanTreasuryCategoryResource extends Resource
{
    protected static ?string $model = ClanTreasuryCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kassen-Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Kasse';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('entries_count')->counts('entries')->label('Einträge'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanTreasuryCategoryResource\Pages\ListClanTreasuryCategories::route('/'),
            'create' => \App\Filament\Resources\ClanTreasuryCategoryResource\Pages\CreateClanTreasuryCategory::route('/create'),
            'edit' => \App\Filament\Resources\ClanTreasuryCategoryResource\Pages\EditClanTreasuryCategory::route('/{record}/edit'),
        ];
    }
}

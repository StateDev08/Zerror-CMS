<?php

namespace App\Filament\Resources;

use App\Models\ClanBankCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ClanBankCategoryResource extends Resource
{
    protected static ?string $model = ClanBankCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Bank-Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Bank';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('items_count')->counts('items')->label('Items'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanBankCategoryResource\Pages\ListClanBankCategories::route('/'),
            'create' => \App\Filament\Resources\ClanBankCategoryResource\Pages\CreateClanBankCategory::route('/create'),
            'edit' => \App\Filament\Resources\ClanBankCategoryResource\Pages\EditClanBankCategory::route('/{record}/edit'),
        ];
    }
}

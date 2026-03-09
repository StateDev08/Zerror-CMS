<?php

namespace App\Filament\Resources;

use App\Models\MarketplaceCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MarketplaceCategoryResource extends Resource
{
    protected static ?string $model = MarketplaceCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Game Marketplace';

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
                TextColumn::make('listings_count')->counts('listings')->label('Anzeigen'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MarketplaceCategoryResource\Pages\ListMarketplaceCategories::route('/'),
            'create' => \App\Filament\Resources\MarketplaceCategoryResource\Pages\CreateMarketplaceCategory::route('/create'),
            'edit' => \App\Filament\Resources\MarketplaceCategoryResource\Pages\EditMarketplaceCategory::route('/{record}/edit'),
        ];
    }
}

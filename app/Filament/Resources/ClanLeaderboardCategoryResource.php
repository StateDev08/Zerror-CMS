<?php

namespace App\Filament\Resources;

use App\Models\ClanLeaderboardCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ClanLeaderboardCategoryResource extends Resource
{
    protected static ?string $model = ClanLeaderboardCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Ranglisten-Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Rangliste';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            TextInput::make('season')->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('season'),
                TextColumn::make('entries_count')->counts('entries')->label('Einträge'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanLeaderboardCategoryResource\Pages\ListClanLeaderboardCategories::route('/'),
            'create' => \App\Filament\Resources\ClanLeaderboardCategoryResource\Pages\CreateClanLeaderboardCategory::route('/create'),
            'edit' => \App\Filament\Resources\ClanLeaderboardCategoryResource\Pages\EditClanLeaderboardCategory::route('/{record}/edit'),
        ];
    }
}

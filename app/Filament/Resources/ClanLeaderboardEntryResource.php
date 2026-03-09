<?php

namespace App\Filament\Resources;

use App\Models\ClanLeaderboardEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanLeaderboardEntryResource extends Resource
{
    protected static ?string $model = ClanLeaderboardEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Ranglisten-Einträge';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Rangliste';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('clan_leaderboard_category_id')->relationship('category', 'name')->required()->label('Kategorie'),
            TextInput::make('player_name')->required(),
            TextInput::make('score')->numeric()->default(0),
            TextInput::make('rank')->numeric()->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('player_name'),
                TextColumn::make('score'),
                TextColumn::make('rank'),
                TextColumn::make('order'),
            ])
            ->defaultSort('category_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanLeaderboardEntryResource\Pages\ListClanLeaderboardEntries::route('/'),
            'create' => \App\Filament\Resources\ClanLeaderboardEntryResource\Pages\CreateClanLeaderboardEntry::route('/create'),
            'edit' => \App\Filament\Resources\ClanLeaderboardEntryResource\Pages\EditClanLeaderboardEntry::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\ClanTeamMember;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanTeamMemberResource extends Resource
{
    protected static ?string $model = ClanTeamMember::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Team-Mitglieder';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('clan_team_id')->relationship('clanTeam', 'name')->required(),
            Select::make('clan_member_id')->relationship('clanMember', 'display_name')->nullable(),
            TextInput::make('display_name')->required(),
            TextInput::make('role')->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clanTeam.name')->label('Team'),
                TextColumn::make('display_name')->label('Name'),
                TextColumn::make('role'),
                TextColumn::make('order'),
            ])
            ->defaultSort('clan_team_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanTeamMemberResource\Pages\ListClanTeamMembers::route('/'),
            'create' => \App\Filament\Resources\ClanTeamMemberResource\Pages\CreateClanTeamMember::route('/create'),
            'edit' => \App\Filament\Resources\ClanTeamMemberResource\Pages\EditClanTeamMember::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\ClanMember;
use App\Models\Rank;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ClanMemberResource extends Resource
{
    protected static ?string $model = ClanMember::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Mitglieder';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('display_name')->required(),
            Select::make('rank_id')->relationship('rank', 'name')->required(),
            TextInput::make('position')->nullable(),
            FileUpload::make('avatar')->image()->directory('avatars')->nullable(),
            Toggle::make('visible')->default(true),
            TextInput::make('order')->numeric()->default(0),
            Select::make('user_id')->relationship('user', 'name')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')->rounded(),
                TextColumn::make('display_name'),
                TextColumn::make('rank.name')->label('Rang'),
                TextColumn::make('position'),
                ToggleColumn::make('visible'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanMemberResource\Pages\ListClanMembers::route('/'),
            'create' => \App\Filament\Resources\ClanMemberResource\Pages\CreateClanMember::route('/create'),
            'edit' => \App\Filament\Resources\ClanMemberResource\Pages\EditClanMember::route('/{record}/edit'),
        ];
    }
}

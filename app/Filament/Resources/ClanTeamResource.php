<?php

namespace App\Filament\Resources;

use App\Models\ClanTeam;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\CmsRichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ClanTeamResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = ClanTeam::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Teams';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            CmsRichEditor::make('description')->nullable(),
            TextInput::make('contact')->nullable(),
            Toggle::make('visible')->default(true),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('members_count')->counts('members')->label('Mitglieder'),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanTeamResource\Pages\ListClanTeams::route('/'),
            'create' => \App\Filament\Resources\ClanTeamResource\Pages\CreateClanTeam::route('/create'),
            'edit' => \App\Filament\Resources\ClanTeamResource\Pages\EditClanTeam::route('/{record}/edit'),
        ];
    }
}

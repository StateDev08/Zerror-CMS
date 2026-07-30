<?php

namespace App\Filament\Resources;

use App\Filament\Forms\CmsFileUpload;
use App\Models\MusicTrack;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MusicTrackResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = MusicTrack::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationLabel = 'Musik';

    protected static ?string $modelLabel = 'Titel';

    protected static ?string $pluralModelLabel = 'Musik';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titel')
                ->required()
                ->maxLength(255),
            TextInput::make('artist')
                ->label('Interpret')
                ->nullable()
                ->maxLength(255),
            CmsFileUpload::audio('file_path', 'music')
                ->label('MP3-Datei')
                ->required(),
            TextInput::make('order')
                ->label('Reihenfolge')
                ->numeric()
                ->default(0),
            Toggle::make('active')
                ->label('Aktiv')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titel')->searchable()->limit(40),
                TextColumn::make('artist')->label('Interpret')->placeholder('—')->limit(30),
                TextColumn::make('order')->label('Reihenfolge')->sortable(),
                IconColumn::make('active')->label('Aktiv')->boolean(),
                TextColumn::make('updated_at')->label('Aktualisiert')->dateTime('d.m.Y H:i')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MusicTrackResource\Pages\ListMusicTracks::route('/'),
            'create' => \App\Filament\Resources\MusicTrackResource\Pages\CreateMusicTrack::route('/create'),
            'edit' => \App\Filament\Resources\MusicTrackResource\Pages\EditMusicTrack::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\GalleryAlbum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GalleryAlbumResource extends Resource
{
    protected static ?string $model = GalleryAlbum::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Alben';

    protected static \UnitEnum|string|null $navigationGroup = 'Galerie';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            Textarea::make('description')->nullable()->rows(3),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('images_count')->counts('images')->label('Bilder'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GalleryAlbumResource\Pages\ListGalleryAlbums::route('/'),
            'create' => \App\Filament\Resources\GalleryAlbumResource\Pages\CreateGalleryAlbum::route('/create'),
            'edit' => \App\Filament\Resources\GalleryAlbumResource\Pages\EditGalleryAlbum::route('/{record}/edit'),
        ];
    }
}

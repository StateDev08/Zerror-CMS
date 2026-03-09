<?php

namespace App\Filament\Resources;

use App\Models\GalleryImage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Bilder';

    protected static \UnitEnum|string|null $navigationGroup = 'Galerie';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('album_id')->relationship('album', 'name')->required(),
            FileUpload::make('path')->image()->directory('gallery')->disk('public')->required(),
            TextInput::make('title')->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')->disk('public')->label('Bild'),
                TextColumn::make('album.name')->label('Album'),
                TextColumn::make('title'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GalleryImageResource\Pages\ListGalleryImages::route('/'),
            'create' => \App\Filament\Resources\GalleryImageResource\Pages\CreateGalleryImage::route('/create'),
            'edit' => \App\Filament\Resources\GalleryImageResource\Pages\EditGalleryImage::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\Download;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Dateien';

    protected static \UnitEnum|string|null $navigationGroup = 'Downloads';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->required(),
            TextInput::make('name')->required(),
            FileUpload::make('file_path')->directory('downloads')->disk('public')->required(),
            TextInput::make('version')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('version'),
                TextColumn::make('download_count')->label('Downloads'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DownloadResource\Pages\ListDownloads::route('/'),
            'create' => \App\Filament\Resources\DownloadResource\Pages\CreateDownload::route('/create'),
            'edit' => \App\Filament\Resources\DownloadResource\Pages\EditDownload::route('/{record}/edit'),
        ];
    }
}

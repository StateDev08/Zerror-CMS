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
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Download::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Downloads';

    protected static \UnitEnum|string|null $navigationGroup = 'Community';

    protected static ?int $navigationSort = 45;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->required(),
            TextInput::make('name')->required(),
            FileUpload::make('file_path')
                ->directory('downloads')
                ->disk('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/zip', 'application/x-zip-compressed',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain', 'text/csv',
                ])
                ->maxSize(fn () => \App\Support\UploadLimits::fileKb())
                ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => \App\Support\UploadLimits::fileMb()]))
                ->required(),
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

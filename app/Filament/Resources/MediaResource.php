<?php

namespace App\Filament\Resources;

use App\Models\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Media::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Medien';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->disk('public')
                ->directory('media')
                ->acceptedFileTypes([
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf', 'video/mp4', 'audio/mpeg',
                ])
                ->maxSize(fn () => \App\Support\UploadLimits::fileKb())
                ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => \App\Support\UploadLimits::fileMb()]))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')->limit(40),
                TextColumn::make('mime_type'),
                TextColumn::make('size')->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : '—'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MediaResource\Pages\ListMedia::route('/'),
            'create' => \App\Filament\Resources\MediaResource\Pages\CreateMedia::route('/create'),
            'edit' => \App\Filament\Resources\MediaResource\Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}

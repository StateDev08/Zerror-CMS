<?php

namespace App\Filament\Resources;

use App\Models\DownloadCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DownloadCategoryResource extends Resource
{
    protected static ?string $model = DownloadCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Downloads';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('downloads_count')->counts('downloads')->label('Dateien'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DownloadCategoryResource\Pages\ListDownloadCategories::route('/'),
            'create' => \App\Filament\Resources\DownloadCategoryResource\Pages\CreateDownloadCategory::route('/create'),
            'edit' => \App\Filament\Resources\DownloadCategoryResource\Pages\EditDownloadCategory::route('/{record}/edit'),
        ];
    }
}

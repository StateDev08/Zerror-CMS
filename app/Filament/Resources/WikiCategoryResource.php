<?php

namespace App\Filament\Resources;

use App\Models\WikiCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WikiCategoryResource extends Resource
{
    protected static ?string $model = WikiCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Wiki-Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Wiki';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            Textarea::make('description')->nullable()->rows(2),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('articles_count')->counts('articles')->label('Artikel'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\WikiCategoryResource\Pages\ListWikiCategories::route('/'),
            'create' => \App\Filament\Resources\WikiCategoryResource\Pages\CreateWikiCategory::route('/create'),
            'edit' => \App\Filament\Resources\WikiCategoryResource\Pages\EditWikiCategory::route('/{record}/edit'),
        ];
    }
}

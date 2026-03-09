<?php

namespace App\Filament\Resources;

use App\Models\WikiArticle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WikiArticleResource extends Resource
{
    protected static ?string $model = WikiArticle::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Wiki-Artikel';

    protected static \UnitEnum|string|null $navigationGroup = 'Wiki';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->nullable(),
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            Textarea::make('body')->required()->rows(12)->columnSpanFull(),
            Toggle::make('published')->default(true),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(40),
                TextColumn::make('category.name')->label('Kategorie'),
                IconColumn::make('published')->boolean(),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\WikiArticleResource\Pages\ListWikiArticles::route('/'),
            'create' => \App\Filament\Resources\WikiArticleResource\Pages\CreateWikiArticle::route('/create'),
            'edit' => \App\Filament\Resources\WikiArticleResource\Pages\EditWikiArticle::route('/{record}/edit'),
        ];
    }
}

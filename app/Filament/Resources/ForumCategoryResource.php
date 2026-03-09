<?php

namespace App\Filament\Resources;

use App\Models\ForumCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ForumCategoryResource extends Resource
{
    protected static ?string $model = ForumCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Forum-Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Forum';

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
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ForumCategoryResource\Pages\ListForumCategories::route('/'),
            'create' => \App\Filament\Resources\ForumCategoryResource\Pages\CreateForumCategory::route('/create'),
            'edit' => \App\Filament\Resources\ForumCategoryResource\Pages\EditForumCategory::route('/{record}/edit'),
        ];
    }
}

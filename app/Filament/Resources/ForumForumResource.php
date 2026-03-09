<?php

namespace App\Filament\Resources;

use App\Models\ForumForum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ForumForumResource extends Resource
{
    protected static ?string $model = ForumForum::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Forum-Foren';

    protected static \UnitEnum|string|null $navigationGroup = 'Forum';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->required(),
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            Textarea::make('description')->nullable()->rows(3),
            TextInput::make('order')->numeric()->default(0),
            Select::make('read_rank_id')->relationship('readRank', 'name')->label('Lese-Berechtigung (Rang)')->nullable(),
            Select::make('write_rank_id')->relationship('writeRank', 'name')->label('Schreib-Berechtigung (Rang)')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ForumForumResource\Pages\ListForumForums::route('/'),
            'create' => \App\Filament\Resources\ForumForumResource\Pages\CreateForumForum::route('/create'),
            'edit' => \App\Filament\Resources\ForumForumResource\Pages\EditForumForum::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\Post;
use App\Filament\Forms\CmsRichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Post::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'News & Seiten';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            CmsRichEditor::make('content')->nullable(),
            Select::make('type')->options(['news' => 'News', 'page' => 'Seite'])->default('news')->required(),
            Toggle::make('published')->default(false),
            Select::make('user_id')->relationship('author', 'name')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'page' ? 'Seite' : 'News'),
                ToggleColumn::make('published'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options(['news' => 'News', 'page' => 'Seite']),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PostResource\Pages\ListPosts::route('/'),
            'create' => \App\Filament\Resources\PostResource\Pages\CreatePost::route('/create'),
            'edit' => \App\Filament\Resources\PostResource\Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

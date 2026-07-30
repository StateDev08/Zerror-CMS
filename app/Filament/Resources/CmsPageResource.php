<?php

namespace App\Filament\Resources;

use App\Filament\Forms\CmsRichEditor;
use App\Models\CmsPage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CmsPageResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = CmsPage::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Seiten';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 11;

    protected static ?string $modelLabel = 'Seite';

    protected static ?string $pluralModelLabel = 'Seiten';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label(__('cms_pages.title'))
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug((string) $state))),
            TextInput::make('slug')
                ->label(__('cms_pages.slug'))
                ->required()
                ->unique(ignoreRecord: true),
            CmsRichEditor::make('content')
                ->label(__('cms_pages.content'))
                ->nullable(),
            Toggle::make('published')
                ->label(__('cms_pages.published'))
                ->default(false),
            TextInput::make('meta_title')
                ->label(__('cms_pages.meta_title'))
                ->maxLength(255)
                ->nullable(),
            Textarea::make('meta_description')
                ->label(__('cms_pages.meta_description'))
                ->rows(3)
                ->maxLength(500)
                ->nullable(),
            Select::make('user_id')
                ->label(__('cms_pages.author'))
                ->relationship('author', 'name')
                ->nullable()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('cms_pages.title'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('cms_pages.slug'))->searchable(),
                ToggleColumn::make('published')->label(__('cms_pages.published')),
                TextColumn::make('updated_at')->label(__('cms_pages.updated'))->dateTime()->sortable(),
            ])
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CmsPageResource\Pages\ListCmsPages::route('/'),
            'create' => \App\Filament\Resources\CmsPageResource\Pages\CreateCmsPage::route('/create'),
            'edit' => \App\Filament\Resources\CmsPageResource\Pages\EditCmsPage::route('/{record}/edit'),
        ];
    }
}

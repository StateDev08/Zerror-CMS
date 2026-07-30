<?php

namespace App\Filament\Resources;

use App\Models\ClanDocument;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use App\Filament\Forms\CmsRichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanDocumentResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = ClanDocument::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Dokumente';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    protected static ?int $navigationSort = 105;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('clan_document_category_id')->relationship('category', 'name')->nullable()->label('Kategorie'),
            TextInput::make('title')->required(),
            CmsRichEditor::make('content')->nullable(),
            FileUpload::make('file_path')
                ->label('Datei')
                ->directory('clan-documents')
                ->disk('public')
                ->singleFile()
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'text/csv'])
                ->maxSize(fn () => \App\Support\UploadLimits::fileKb())
                ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => \App\Support\UploadLimits::fileMb()])),
            Toggle::make('visible')->default(true),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(40),
                TextColumn::make('category.name')->label('Kategorie'),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanDocumentResource\Pages\ListClanDocuments::route('/'),
            'create' => \App\Filament\Resources\ClanDocumentResource\Pages\CreateClanDocument::route('/create'),
            'edit' => \App\Filament\Resources\ClanDocumentResource\Pages\EditClanDocument::route('/{record}/edit'),
        ];
    }
}

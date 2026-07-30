<?php

namespace App\Filament\Resources;

use App\Filament\Forms\CmsRichEditor;

use App\Models\Partner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Partner::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationLabel = 'Partner';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('url')->url()->required(),
            FileUpload::make('logo')
                ->image()
                ->directory('partners')
                ->disk('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                ->maxSize(fn () => \App\Support\UploadLimits::imageKb())
                ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => \App\Support\UploadLimits::imageMb()])),
            CmsRichEditor::compact('description')->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->disk('public')->label('Logo'),
                TextColumn::make('name'),
                TextColumn::make('url')->limit(30),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PartnerResource\Pages\ListPartners::route('/'),
            'create' => \App\Filament\Resources\PartnerResource\Pages\CreatePartner::route('/create'),
            'edit' => \App\Filament\Resources\PartnerResource\Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}

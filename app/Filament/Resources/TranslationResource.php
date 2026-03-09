<?php

namespace App\Filament\Resources;

use App\Models\Translation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Übersetzungen';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('locale')->options(['de' => 'Deutsch', 'en' => 'English'])->required(),
            TextInput::make('key')->required(),
            Textarea::make('value')->nullable()->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('locale'),
                TextColumn::make('key'),
                TextColumn::make('value')->limit(50),
            ])
            ->defaultSort('key')
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\TranslationResource\Pages\ListTranslations::route('/'),
            'create' => \App\Filament\Resources\TranslationResource\Pages\CreateTranslation::route('/create'),
            'edit' => \App\Filament\Resources\TranslationResource\Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}

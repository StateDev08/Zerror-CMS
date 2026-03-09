<?php

namespace App\Filament\Resources;

use App\Models\ClanBankItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanBankItemResource extends Resource
{
    protected static ?string $model = ClanBankItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Bank-Items';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Bank';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('clan_bank_category_id')->relationship('category', 'name')->nullable()->label('Kategorie'),
            TextInput::make('name')->required(),
            Textarea::make('description')->nullable()->rows(3),
            TextInput::make('quantity')->numeric()->default(1),
            TextInput::make('location')->nullable(),
            Toggle::make('visible')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('quantity'),
                TextColumn::make('location')->limit(20),
                IconColumn::make('visible')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanBankItemResource\Pages\ListClanBankItems::route('/'),
            'create' => \App\Filament\Resources\ClanBankItemResource\Pages\CreateClanBankItem::route('/create'),
            'edit' => \App\Filament\Resources\ClanBankItemResource\Pages\EditClanBankItem::route('/{record}/edit'),
        ];
    }
}

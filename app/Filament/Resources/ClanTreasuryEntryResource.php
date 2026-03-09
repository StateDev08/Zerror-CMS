<?php

namespace App\Filament\Resources;

use App\Models\ClanTreasuryEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanTreasuryEntryResource extends Resource
{
    protected static ?string $model = ClanTreasuryEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-euro';

    protected static ?string $navigationLabel = 'Kassenbuch';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan Kasse';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->options(['income' => 'Einnahme', 'expense' => 'Ausgabe'])->required(),
            TextInput::make('amount')->numeric()->required()->prefix('€'),
            Select::make('clan_treasury_category_id')->relationship('category', 'name')->nullable()->label('Kategorie'),
            TextInput::make('title')->nullable(),
            Textarea::make('note')->nullable()->rows(2),
            DatePicker::make('entry_date')->required()->default(now()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_date')->date()->sortable(),
                TextColumn::make('type')->badge()->formatStateUsing(fn ($s) => $s === 'income' ? 'Einnahme' : 'Ausgabe'),
                TextColumn::make('amount')->money('EUR'),
                TextColumn::make('title')->limit(30),
                TextColumn::make('category.name')->label('Kategorie'),
            ])
            ->defaultSort('entry_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanTreasuryEntryResource\Pages\ListClanTreasuryEntries::route('/'),
            'create' => \App\Filament\Resources\ClanTreasuryEntryResource\Pages\CreateClanTreasuryEntry::route('/create'),
            'edit' => \App\Filament\Resources\ClanTreasuryEntryResource\Pages\EditClanTreasuryEntry::route('/{record}/edit'),
        ];
    }
}

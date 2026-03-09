<?php

namespace App\Filament\Resources;

use App\Models\CraftableItem;
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

class CraftableItemResource extends Resource
{
    protected static ?string $model = CraftableItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Craftbare Items';

    protected static ?string $modelLabel = 'Craftbares Item';

    protected static ?string $pluralModelLabel = 'Craftbare Items';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->nullable(),
            Textarea::make('description')->nullable()->rows(3),
            Select::make('category')
                ->options([
                    'Waffe' => __('crafting.category_weapon'),
                    'Rüstung' => __('crafting.category_armor'),
                    'Sonstiges' => __('crafting.category_other'),
                ])
                ->default('Sonstiges'),
            TextInput::make('order')->numeric()->default(0),
            Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('category')->label(__('crafting.category')),
                TextColumn::make('order'),
                IconColumn::make('active')->boolean(),
                TextColumn::make('item_requests_count')->counts('itemRequests')->label(__('crafting.requests_count')),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CraftableItemResource\Pages\ListCraftableItems::route('/'),
            'create' => \App\Filament\Resources\CraftableItemResource\Pages\CreateCraftableItem::route('/create'),
            'edit' => \App\Filament\Resources\CraftableItemResource\Pages\EditCraftableItem::route('/{record}/edit'),
        ];
    }
}

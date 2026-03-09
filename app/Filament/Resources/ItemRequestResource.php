<?php

namespace App\Filament\Resources;

use App\Models\ItemRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ItemRequestResource extends Resource
{
    protected static ?string $model = ItemRequest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Item-Aufträge';

    protected static ?string $modelLabel = 'Auftrag';

    protected static ?string $pluralModelLabel = 'Item-Aufträge';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->options(ItemRequest::statusLabels())
                ->required(),
            TextInput::make('max_price')->numeric()->minValue(0)->nullable()->label(__('crafting.max_price')),
            DatePicker::make('desired_date')->nullable()->label(__('crafting.desired_date')),
            Select::make('priority')->options(ItemRequest::priorityLabels())->label(__('crafting.priority')),
            TextInput::make('quantity')->numeric()->minValue(1)->default(1)->label(__('crafting.quantity')),
            Textarea::make('admin_notes')->nullable()->rows(4)->label(__('crafting.admin_notes')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label(__('crafting.user'))->searchable(),
                TextColumn::make('craftableItem.name')->label(__('crafting.item'))->placeholder(__('general.no_value')),
                TextColumn::make('custom_request')->limit(40)->placeholder(__('general.no_value')),
                TextColumn::make('quantity')->label(__('crafting.quantity'))->sortable(),
                TextColumn::make('max_price')->label(__('crafting.max_price'))->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 0, ',', '.') : __('general.no_value'))->sortable(),
                TextColumn::make('desired_date')->label(__('crafting.desired_date'))->date()->placeholder(__('general.no_value'))->sortable(),
                TextColumn::make('priority')->label(__('crafting.priority'))->formatStateUsing(fn (?string $state) => ItemRequest::priorityLabels()[$state ?? 'normal'] ?? $state)->sortable(),
                TextColumn::make('status')->badge()->formatStateUsing(fn (string $state) => ItemRequest::statusLabels()[$state] ?? $state),
                TextColumn::make('created_at')->label(__('crafting.date'))->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ItemRequest::statusLabels()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ItemRequestResource\Pages\ListItemRequests::route('/'),
            'edit' => \App\Filament\Resources\ItemRequestResource\Pages\EditItemRequest::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

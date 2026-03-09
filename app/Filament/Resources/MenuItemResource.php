<?php

namespace App\Filament\Resources;

use App\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $navigationLabel = 'Menüeinträge';

    protected static ?string $modelLabel = 'Menüeintrag';

    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('position')
                ->options(['left' => 'Links', 'right' => 'Rechts'])
                ->default('left')
                ->required(),
            TextInput::make('label')->required()->maxLength(255),
            TextInput::make('link')
                ->required()
                ->maxLength(500)
                ->placeholder(__('menu.link_placeholder')),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_visible')->default(true)->label('Sichtbar'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')->badge()->color(fn (string $state) => $state === 'left' ? 'info' : 'success'),
                TextColumn::make('label'),
                TextColumn::make('link')->limit(40),
                TextColumn::make('sort_order'),
                ToggleColumn::make('is_visible')->label('Sichtbar'),
            ])
            ->filters([
                SelectFilter::make('position')->options(['left' => 'Links', 'right' => 'Rechts']),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MenuItemResource\Pages\ListMenuItems::route('/'),
            'create' => \App\Filament\Resources\MenuItemResource\Pages\CreateMenuItem::route('/create'),
            'edit' => \App\Filament\Resources\MenuItemResource\Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}

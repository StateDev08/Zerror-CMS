<?php

namespace App\Filament\Resources;

use App\Models\MenuItem;
use App\Support\MenuTargets;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = MenuItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $navigationLabel = 'Menüeinträge';

    protected static ?string $modelLabel = 'Menüeintrag';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('position')
                ->label(__('menu.position'))
                ->options(MenuTargets::positions())
                ->default('top')
                ->required()
                ->helperText(__('menu.position_help')),
            TextInput::make('label')
                ->label(__('menu.label'))
                ->required()
                ->maxLength(255),
            Toggle::make('use_custom_link')
                ->label(__('menu.use_custom_link'))
                ->helperText(__('menu.use_custom_link_help'))
                ->dehydrated(false)
                ->live()
                ->afterStateHydrated(function (Toggle $component, mixed $state, ?MenuItem $record): void {
                    if ($record && ! MenuTargets::isKnown((string) $record->link)) {
                        $component->state(true);
                    }
                }),
            Select::make('link')
                ->label(__('menu.link_target'))
                ->options(fn (): array => MenuTargets::groupedOptions())
                ->searchable()
                ->required(fn (Get $get): bool => ! (bool) $get('use_custom_link'))
                ->visible(fn (Get $get): bool => ! (bool) $get('use_custom_link'))
                ->helperText(__('menu.link_target_help')),
            TextInput::make('custom_link')
                ->label(__('menu.custom_link'))
                ->maxLength(500)
                ->placeholder(__('menu.link_placeholder'))
                ->required(fn (Get $get): bool => (bool) $get('use_custom_link'))
                ->visible(fn (Get $get): bool => (bool) $get('use_custom_link'))
                ->dehydrated(false)
                ->afterStateHydrated(function (TextInput $component, mixed $state, ?MenuItem $record): void {
                    if ($record && ! MenuTargets::isKnown((string) $record->link)) {
                        $component->state($record->link);
                    }
                }),
            TextInput::make('sort_order')
                ->label(__('menu.sort_order'))
                ->numeric()
                ->default(0),
            Toggle::make('is_visible')
                ->default(true)
                ->label(__('menu.visible')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')
                    ->label(__('menu.position'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => MenuTargets::positions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'top' => 'warning',
                        'left' => 'info',
                        'right' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('label')->label(__('menu.label')),
                TextColumn::make('link')
                    ->label(__('menu.link_target'))
                    ->formatStateUsing(fn (string $state): string => MenuTargets::labelFor($state) ?? $state)
                    ->limit(40)
                    ->tooltip(fn (MenuItem $record): string => $record->link),
                TextColumn::make('sort_order')->label(__('menu.sort_order')),
                ToggleColumn::make('is_visible')->label(__('menu.visible')),
            ])
            ->filters([
                SelectFilter::make('position')->options(MenuTargets::positions()),
            ])
            ->emptyStateHeading(__('zerrocms.menu.empty_title'))
            ->emptyStateDescription(__('zerrocms.menu.empty_body'))
            ->emptyStateIcon('heroicon-o-bars-3-bottom-left')
            ->emptyStateActions([
                CreateAction::make()->label(__('zerrocms.menu.empty_action')),
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

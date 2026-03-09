<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Berechtigungen';

    protected static ?string $modelLabel = 'Berechtigung';

    protected static ?string $pluralModelLabel = 'Berechtigungen';

    protected static \UnitEnum|string|null $navigationGroup = 'Rechteverwaltung';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->label('Name'),
            Select::make('guard_name')
                ->options(['web' => 'Web'])
                ->default('web')
                ->required()
                ->label('Guard'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('guard_name')->label('Guard'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PermissionResource\Pages\ListPermissions::route('/'),
            'create' => \App\Filament\Resources\PermissionResource\Pages\CreatePermission::route('/create'),
            'edit' => \App\Filament\Resources\PermissionResource\Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}

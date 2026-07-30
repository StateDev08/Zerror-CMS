<?php

namespace App\Filament\Resources;

use App\Support\PermissionCatalog;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Role::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Rollen';

    protected static ?string $modelLabel = 'Rolle';

    protected static ?string $pluralModelLabel = 'Rollen';

    protected static \UnitEnum|string|null $navigationGroup = 'Benutzer';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        $labels = PermissionCatalog::labels();

        return $schema->components([
            Section::make('Rolle')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->label('Name'),
                    Select::make('guard_name')
                        ->options(['web' => 'Web'])
                        ->default('web')
                        ->required()
                        ->label('Guard'),
                ]),
            Section::make('Berechtigungen')
                ->description('Rechte für alle CMS-Bereiche. Super-Admin besitzt automatisch alles.')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Rechte')
                        ->relationship(
                            name: 'permissions',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Permission $record): string => $labels[$record->name] ?? $record->name
                        )
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('guard_name')->label('Guard'),
                TextColumn::make('permissions_count')->counts('permissions')->label('Berechtigungen'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\RoleResource\Pages\ListRoles::route('/'),
            'create' => \App\Filament\Resources\RoleResource\Pages\CreateRole::route('/create'),
            'edit' => \App\Filament\Resources\RoleResource\Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

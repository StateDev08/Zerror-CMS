<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\UserNotification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserNotificationResource extends Resource
{
    protected static ?string $model = UserNotification::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Benachrichtigungen';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->relationship('user', 'name')->searchable()->required(),
            TextInput::make('message')->required()->columnSpanFull(),
            TextInput::make('link')->url()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Benutzer'),
                TextColumn::make('message')->limit(50),
                TextColumn::make('read_at')->dateTime()->placeholder(__('general.unread'))->label('Gelesen'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserNotificationResource\Pages\ListUserNotifications::route('/'),
            'create' => \App\Filament\Resources\UserNotificationResource\Pages\CreateUserNotification::route('/create'),
            'edit' => \App\Filament\Resources\UserNotificationResource\Pages\EditUserNotification::route('/{record}/edit'),
        ];
    }
}

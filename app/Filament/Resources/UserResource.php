<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Benutzer';

    protected static ?string $modelLabel = 'Benutzer';

    protected static ?string $pluralModelLabel = 'Benutzer';

    protected static \UnitEnum|string|null $navigationGroup = 'Rechteverwaltung';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->maxLength(255),
            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255)
                ->label('Passwort'),
            FileUpload::make('avatar')->image()->directory('profiles')->disk('public')->maxSize(2048)->nullable()->label('Profilbild'),
            TextInput::make('job')->maxLength(191)->nullable()->label('Beruf / Tätigkeit'),
            Textarea::make('biography')->maxLength(2000)->rows(4)->nullable()->label('Biografie'),
            Textarea::make('about_me')->maxLength(5000)->rows(6)->nullable()->label('Über mich'),
            TextInput::make('location')->maxLength(191)->nullable()->label('Standort'),
            TextInput::make('website')->url()->maxLength(255)->nullable()->label('Website'),
            TextInput::make('discord_handle')->maxLength(191)->nullable()->label('Discord'),
            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->label('Rollen'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')->disk('public')->rounded()->label(''),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('job')->limit(25)->placeholder(__('general.no_value')),
                TextColumn::make('roles.name')->label('Rollen')->badge(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

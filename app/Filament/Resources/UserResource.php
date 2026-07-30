<?php

namespace App\Filament\Resources;

use App\Filament\Forms\CmsRichEditor;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Benutzer';

    protected static ?string $modelLabel = 'Benutzer';

    protected static ?string $pluralModelLabel = 'Benutzer';

    protected static \UnitEnum|string|null $navigationGroup = 'Benutzer';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->maxLength(255),
            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255)
                ->rule(\Illuminate\Validation\Rules\Password::defaults())
                ->label('Passwort'),
            FileUpload::make('avatar')
                ->image()
                ->directory('profiles')
                ->disk('public')
                ->maxSize(fn () => \App\Support\UploadLimits::imageKb())
                ->helperText(fn () => __('settings.upload_limit_hint', ['mb' => \App\Support\UploadLimits::imageMb()]))
                ->nullable()
                ->label('Profilbild'),
            TextInput::make('job')->maxLength(191)->nullable()->label('Beruf / Tätigkeit'),
            CmsRichEditor::compact('biography')->nullable()->label('Biografie'),
            CmsRichEditor::compact('about_me')->nullable()->label('Über mich'),
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

<?php

namespace App\Filament\Resources;

use App\Models\DiscordQuickCommand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class DiscordQuickCommandResource extends Resource
{
    protected static ?string $model = DiscordQuickCommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationLabel = 'Discord Quick-Befehle';

    protected static ?string $modelLabel = 'Quick-Befehl';

    protected static ?string $pluralModelLabel = 'Quick-Befehle';

    protected static ?string $navigationGroup = 'Discord';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(32)
                ->helperText('Nur Kleinbuchstaben, Zahlen, Unterstriche (z. B. spitzhacke). Wird zum Slash-Befehl /<name>.')
                ->afterStateUpdated(fn ($state, $set) => $set('name', DiscordQuickCommand::normalizeName($state ?? ''))),
            TextInput::make('description')
                ->maxLength(100)
                ->helperText('Kurzbeschreibung in Discord (optional).'),
            Textarea::make('response_text')
                ->required()
                ->rows(4)
                ->helperText('Diese Nachricht antwortet der Bot, wenn jemand den Befehl nutzt.'),
            Select::make('created_by')
                ->relationship('creator', 'name')
                ->nullable()
                ->searchable(),
            Toggle::make('is_public')->default(true)->label('Öffentlich (für alle nutzbar)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->badge()->searchable(),
                TextColumn::make('description')->limit(40)->placeholder('—'),
                TextColumn::make('response_text')->limit(35)->placeholder('—'),
                IconColumn::make('is_public')->boolean()->label('Öffentlich'),
                TextColumn::make('use_count')->label('Nutzungen')->sortable(),
                TextColumn::make('creator.name')->label('Erstellt von')->placeholder('—'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DiscordQuickCommandResource\Pages\ListDiscordQuickCommands::route('/'),
            'create' => \App\Filament\Resources\DiscordQuickCommandResource\Pages\CreateDiscordQuickCommand::route('/create'),
            'edit' => \App\Filament\Resources\DiscordQuickCommandResource\Pages\EditDiscordQuickCommand::route('/{record}/edit'),
        ];
    }
}

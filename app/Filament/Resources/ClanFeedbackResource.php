<?php

namespace App\Filament\Resources;

use App\Models\ClanFeedback;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanFeedbackResource extends Resource
{
    protected static ?string $model = ClanFeedback::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Clan Feedback';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author_name')->required()->disabled(),
            TextInput::make('author_email')->email()->disabled(),
            Textarea::make('message')->required()->disabled()->rows(4),
            Select::make('status')->options([
                'new' => 'Neu',
                'read' => 'Gelesen',
                'done' => 'Erledigt',
            ])->required(),
            Textarea::make('admin_notes')->nullable()->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name'),
                TextColumn::make('message')->limit(40),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanFeedbackResource\Pages\ListClanFeedback::route('/'),
            'edit' => \App\Filament\Resources\ClanFeedbackResource\Pages\EditClanFeedback::route('/{record}/edit'),
        ];
    }
}

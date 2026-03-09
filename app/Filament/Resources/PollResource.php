<?php

namespace App\Filament\Resources;

use App\Models\Poll;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Umfragen';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('question')->required(),
            DateTimePicker::make('ends_at')->nullable(),
            Toggle::make('active')->default(true),
            Repeater::make('options')
                ->relationship()
                ->schema([
                    TextInput::make('text')->required(),
                ])
                ->defaultItems(0)
                ->minItems(1)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->limit(40),
                TextColumn::make('ends_at')->dateTime()->placeholder(__('general.no_value')),
                IconColumn::make('active')->boolean(),
                TextColumn::make('options_count')->counts('options')->label('Optionen'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PollResource\Pages\ListPolls::route('/'),
            'create' => \App\Filament\Resources\PollResource\Pages\CreatePoll::route('/create'),
            'edit' => \App\Filament\Resources\PollResource\Pages\EditPoll::route('/{record}/edit'),
        ];
    }
}

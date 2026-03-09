<?php

namespace App\Filament\Resources;

use App\Models\ClanAnnouncement;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanAnnouncementResource extends Resource
{
    protected static ?string $model = ClanAnnouncement::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Clan Ankündigungen';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            Textarea::make('body')->required()->rows(6)->columnSpanFull(),
            Toggle::make('visible')->default(true),
            DateTimePicker::make('visible_until')->nullable(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(40),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('visible_until')->dateTime()->placeholder(__('general.no_value')),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanAnnouncementResource\Pages\ListClanAnnouncements::route('/'),
            'create' => \App\Filament\Resources\ClanAnnouncementResource\Pages\CreateClanAnnouncement::route('/create'),
            'edit' => \App\Filament\Resources\ClanAnnouncementResource\Pages\EditClanAnnouncement::route('/{record}/edit'),
        ];
    }
}

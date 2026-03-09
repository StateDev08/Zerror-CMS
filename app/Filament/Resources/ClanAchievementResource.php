<?php

namespace App\Filament\Resources;

use App\Models\ClanAchievement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanAchievementResource extends Resource
{
    protected static ?string $model = ClanAchievement::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Clan Erfolge';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            Textarea::make('description')->nullable()->rows(3),
            TextInput::make('icon')->nullable()->placeholder(__('achievements.icon_placeholder')),
            DatePicker::make('achieved_at')->nullable(),
            Toggle::make('visible')->default(true),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(35),
                TextColumn::make('achieved_at')->date()->placeholder(__('general.no_value')),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanAchievementResource\Pages\ListClanAchievements::route('/'),
            'create' => \App\Filament\Resources\ClanAchievementResource\Pages\CreateClanAchievement::route('/create'),
            'edit' => \App\Filament\Resources\ClanAchievementResource\Pages\EditClanAchievement::route('/{record}/edit'),
        ];
    }
}

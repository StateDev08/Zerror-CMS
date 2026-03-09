<?php

namespace App\Filament\Resources;

use App\Models\SliderSlide;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SliderSlideResource extends Resource
{
    protected static ?string $model = SliderSlide::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Slider';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->nullable(),
            TextInput::make('subtitle')->nullable(),
            FileUpload::make('image')->image()->directory('slider')->disk('public')->required(),
            TextInput::make('link')->url()->nullable(),
            TextInput::make('order')->numeric()->default(0),
            Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public')->label('Bild'),
                TextColumn::make('title')->limit(30),
                TextColumn::make('subtitle')->limit(25),
                TextColumn::make('order'),
                IconColumn::make('active')->boolean(),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SliderSlideResource\Pages\ListSliderSlides::route('/'),
            'create' => \App\Filament\Resources\SliderSlideResource\Pages\CreateSliderSlide::route('/create'),
            'edit' => \App\Filament\Resources\SliderSlideResource\Pages\EditSliderSlide::route('/{record}/edit'),
        ];
    }
}

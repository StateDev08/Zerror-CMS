<?php

namespace App\Filament\Resources;

use App\Models\JobOffer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JobOfferResource extends Resource
{
    protected static ?string $model = JobOffer::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Stellenangebote';

    protected static \UnitEnum|string|null $navigationGroup = 'Stellenangebote';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->nullable(),
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            Textarea::make('description')->nullable()->rows(8)->columnSpanFull(),
            TextInput::make('location')->nullable(),
            Select::make('employment_type')->options([
                'full-time' => 'Vollzeit',
                'part-time' => 'Teilzeit',
                'freelance' => 'Freelance',
                'contract' => 'Vertrag',
            ])->default('full-time'),
            TextInput::make('contact_email')->email()->nullable(),
            Toggle::make('published')->default(true),
            DatePicker::make('expires_at')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(35),
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('employment_type')->label('Art'),
                TextColumn::make('expires_at')->date()->placeholder(__('general.no_value')),
                IconColumn::make('published')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\JobOfferResource\Pages\ListJobOffers::route('/'),
            'create' => \App\Filament\Resources\JobOfferResource\Pages\CreateJobOffer::route('/create'),
            'edit' => \App\Filament\Resources\JobOfferResource\Pages\EditJobOffer::route('/{record}/edit'),
        ];
    }
}

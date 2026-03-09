<?php

namespace App\Filament\Resources;

use App\Models\JobOfferCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JobOfferCategoryResource extends Resource
{
    protected static ?string $model = JobOfferCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Kategorien';

    protected static \UnitEnum|string|null $navigationGroup = 'Stellenangebote';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('job_offers_count')->counts('jobOffers')->label('Stellen'),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\JobOfferCategoryResource\Pages\ListJobOfferCategories::route('/'),
            'create' => \App\Filament\Resources\JobOfferCategoryResource\Pages\CreateJobOfferCategory::route('/create'),
            'edit' => \App\Filament\Resources\JobOfferCategoryResource\Pages\EditJobOfferCategory::route('/{record}/edit'),
        ];
    }
}

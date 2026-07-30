<?php

namespace App\Filament\Resources;

use App\Models\MarketplaceListing;
use Filament\Forms\Components\Select;
use App\Filament\Forms\CmsRichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MarketplaceListingResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = MarketplaceListing::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Anzeigen';

    protected static \UnitEnum|string|null $navigationGroup = 'Markt & Jobs';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->relationship('category', 'name')->nullable(),
            Select::make('user_id')->relationship('user', 'name')->searchable()->nullable()->label('Benutzer'),
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')->required(),
            CmsRichEditor::make('description')->nullable(),
            Select::make('price_type')->options(['free' => 'Kostenlos', 'fixed' => 'Festpreis', 'negotiable' => 'Verhandelbar'])->default('negotiable'),
            TextInput::make('price_value')->numeric()->nullable()->prefix('€'),
            TextInput::make('contact_info')->label('Kontakt')->nullable(),
            Toggle::make('published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(35),
                TextColumn::make('category.name')->label('Kategorie'),
                TextColumn::make('user.name')->label('Benutzer')->placeholder(__('general.no_value')),
                TextColumn::make('price_type')->label('Preis'),
                IconColumn::make('published')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MarketplaceListingResource\Pages\ListMarketplaceListings::route('/'),
            'create' => \App\Filament\Resources\MarketplaceListingResource\Pages\CreateMarketplaceListing::route('/create'),
            'edit' => \App\Filament\Resources\MarketplaceListingResource\Pages\EditMarketplaceListing::route('/{record}/edit'),
        ];
    }
}

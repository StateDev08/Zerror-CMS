<?php

namespace App\Filament\Resources;

use App\Models\NewsletterSubscriber;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Newsletter-Abonnenten';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')->email()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email'),
                TextColumn::make('confirmed_at')->dateTime()->placeholder(__('general.no_value'))->label('Bestätigt'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\NewsletterSubscriberResource\Pages\ListNewsletterSubscribers::route('/'),
            'create' => \App\Filament\Resources\NewsletterSubscriberResource\Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => \App\Filament\Resources\NewsletterSubscriberResource\Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}

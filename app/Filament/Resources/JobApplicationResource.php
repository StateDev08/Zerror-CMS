<?php

namespace App\Filament\Resources;

use App\Models\JobApplication;
use App\Support\HtmlContent;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class JobApplicationResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = JobApplication::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationLabel = 'Bewerbungen';

    protected static \UnitEnum|string|null $navigationGroup = 'Stellenangebote';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('job_offer_id')->relationship('jobOffer', 'title')->disabled(),
            TextInput::make('name')->disabled(),
            TextInput::make('email')->email()->disabled(),
            Placeholder::make('message_html')
                ->label(__('jobs.application_message'))
                ->content(fn (?JobApplication $record): HtmlString => $record
                    ? HtmlContent::toHtml($record->message)
                    : new HtmlString(''))
                ->columnSpanFull(),
            Select::make('user_id')->relationship('user', 'name')->disabled()->label('Benutzer'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jobOffer.title')->label('Stelle')->limit(30),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('user.name')->label('Benutzer')->placeholder(__('general.no_value')),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\JobApplicationResource\Pages\ListJobApplications::route('/'),
            'view' => \App\Filament\Resources\JobApplicationResource\Pages\ViewJobApplication::route('/{record}'),
        ];
    }
}

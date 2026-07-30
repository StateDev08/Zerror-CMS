<?php

namespace App\Filament\Resources;

use App\Filament\Forms\CmsRichEditor;
use App\Models\Application;
use App\Support\HtmlContent;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ApplicationResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = Application::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Clan Bewerbung';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->disabled(),
            TextInput::make('email')->email()->required()->disabled(),
            Placeholder::make('message_html')
                ->label(__('apply.message'))
                ->content(fn (?Application $record): HtmlString => $record
                    ? HtmlContent::toHtml($record->message)
                    : new HtmlString('')),
            Select::make('status')->options([
                'open' => 'Offen',
                'accepted' => 'Angenommen',
                'rejected' => 'Abgelehnt',
            ])->required(),
            CmsRichEditor::compact('notes')->nullable()->label('Notizen'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApplicationResource\Pages\ListApplications::route('/'),
            'edit' => \App\Filament\Resources\ApplicationResource\Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}

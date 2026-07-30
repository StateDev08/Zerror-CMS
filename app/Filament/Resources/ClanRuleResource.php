<?php

namespace App\Filament\Resources;

use App\Models\ClanRule;
use App\Filament\Forms\CmsRichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClanRuleResource extends Resource
{
    use \App\Filament\Concerns\ChecksCmsPermissions;

    protected static ?string $model = ClanRule::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Regeln';

    protected static \UnitEnum|string|null $navigationGroup = 'Clan';

    protected static ?int $navigationSort = 55;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            CmsRichEditor::make('content')->required(),
            Toggle::make('visible')->default(true),
            TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(40),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('order'),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ClanRuleResource\Pages\ListClanRules::route('/'),
            'create' => \App\Filament\Resources\ClanRuleResource\Pages\CreateClanRule::route('/create'),
            'edit' => \App\Filament\Resources\ClanRuleResource\Pages\EditClanRule::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WidgetInstanceResource\Pages;
use App\Models\WidgetInstance;
use App\Widgets\WidgetRegistry;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WidgetInstanceResource extends Resource
{
    protected static ?string $model = WidgetInstance::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Widgets';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    public static function form(Schema $schema): Schema
    {
        $registry = app(WidgetRegistry::class);
        $widgetOptions = collect($registry->all())->mapWithKeys(fn ($w) => [$w->id() => $w->title()])->all();
        $slotOptions = array_combine($registry->slots(), $registry->slots());

        $components = [
            Select::make('slot')
                ->options($slotOptions)
                ->required(),
            Select::make('widget_key')
                ->label('Widget')
                ->options($widgetOptions)
                ->required()
                ->live(),
            TextInput::make('order')
                ->numeric()
                ->default(0),
        ];

        foreach ($registry->all() as $widget) {
            $configSchema = $widget->configSchema();
            if ($configSchema === []) {
                continue;
            }
            $widgetId = $widget->id();
            $sectionComponents = [];
            foreach ($configSchema as $key => $def) {
                $type = $def['type'] ?? 'text';
                $label = $def['label'] ?? $key;
                $default = $def['default'] ?? null;
                $fieldName = 'config.' . $key;
                if ($type === 'boolean') {
                    $sectionComponents[] = Checkbox::make($fieldName)
                        ->label($label)
                        ->default($default)
                        ->visible(fn (Get $get) => $get('widget_key') === $widgetId);
                } elseif ($type === 'number') {
                    $sectionComponents[] = TextInput::make($fieldName)
                        ->label($label)
                        ->numeric()
                        ->default($default)
                        ->visible(fn (Get $get) => $get('widget_key') === $widgetId);
                } elseif ($type === 'url') {
                    $sectionComponents[] = TextInput::make($fieldName)
                        ->label($label)
                        ->url()
                        ->default($default)
                        ->visible(fn (Get $get) => $get('widget_key') === $widgetId);
                } else {
                    $sectionComponents[] = TextInput::make($fieldName)
                        ->label($label)
                        ->default($default)
                        ->visible(fn (Get $get) => $get('widget_key') === $widgetId);
                }
            }
            if ($sectionComponents !== []) {
                $components[] = Section::make($widget->title())
                    ->description('Optionale Einstellungen für dieses Widget')
                    ->schema($sectionComponents)
                    ->visible(fn (Get $get) => $get('widget_key') === $widgetId)
                    ->collapsed(false);
            }
        }

        return $schema->components($components);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slot'),
                TextColumn::make('widget_key')->label('Widget'),
                TextColumn::make('order'),
            ])
            ->defaultSort('slot');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWidgetInstances::route('/'),
            'create' => Pages\CreateWidgetInstance::route('/create'),
            'edit' => Pages\EditWidgetInstance::route('/{record}/edit'),
        ];
    }
}

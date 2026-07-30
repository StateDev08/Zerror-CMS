<?php

use App\Widgets\Contracts\WidgetContract;

/**
 * Beispiel-Widget. Wird von WidgetPackageManager per require geladen.
 * Rückgabe: WidgetContract-Instanz oder Klassenname.
 */
return new class implements WidgetContract
{
    public function id(): string
    {
        return 'example';
    }

    public function title(): string
    {
        return 'Example Widget';
    }

    public function render(array $config = []): string
    {
        $text = trim((string) ($config['text'] ?? 'Hello from example widget'));
        if ($text === '') {
            $text = 'Hello from example widget';
        }

        return '<div class="widget widget-example"><p>'.e($text).'</p></div>';
    }

    public function configSchema(): array
    {
        return [
            'text' => [
                'type' => 'text',
                'label' => 'Text',
                'default' => 'Hello from example widget',
            ],
        ];
    }
};

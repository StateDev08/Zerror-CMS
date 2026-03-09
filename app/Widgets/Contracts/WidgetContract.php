<?php

namespace App\Widgets\Contracts;

interface WidgetContract
{
    public function id(): string;

    public function title(): string;

    public function render(array $config = []): string;

    /**
     * @return array<string, array{type: string, label: string, default?: mixed}>
     */
    public function configSchema(): array;
}

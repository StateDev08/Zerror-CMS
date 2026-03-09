<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Collection;

class WidgetRegistry
{
    /** @var array<string, WidgetContract> */
    protected array $widgets = [];

    /** @var array<string, string[]> */
    protected array $slots = [
        'sidebar' => [],
        'footer' => [],
        'dashboard' => [],
        'home' => [],
    ];

    public function register(WidgetContract $widget, array $slots = ['sidebar']): void
    {
        $this->widgets[$widget->id()] = $widget;
        foreach ($slots as $slot) {
            if (isset($this->slots[$slot]) && ! in_array($widget->id(), $this->slots[$slot], true)) {
                $this->slots[$slot][] = $widget->id();
            }
        }
    }

    public function get(string $id): ?WidgetContract
    {
        return $this->widgets[$id] ?? null;
    }

    /**
     * @return array<string, WidgetContract>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * @return string[]
     */
    public function slots(): array
    {
        return array_keys($this->slots);
    }

    /**
     * @return string[]
     */
    public function forSlot(string $slot): array
    {
        return $this->slots[$slot] ?? [];
    }

    public function addSlot(string $slot): void
    {
        if (! isset($this->slots[$slot])) {
            $this->slots[$slot] = [];
        }
    }
}

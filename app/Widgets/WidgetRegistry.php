<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

class WidgetRegistry
{
    /** @var array<string, WidgetContract> */
    protected array $widgets = [];

    /** @var array<string, string[]> */
    protected array $slots = [
        'left' => [],
        'right' => [],
        'footer' => [],
        'dashboard' => [],
    ];

    /**
     * @param  list<string>  $slots
     */
    public function register(WidgetContract $widget, array $slots = ['left', 'right']): void
    {
        $this->widgets[$widget->id()] = $widget;
        foreach ($this->normalizeSlots($slots) as $slot) {
            $this->addSlot($slot);
            if (! in_array($widget->id(), $this->slots[$slot], true)) {
                $this->slots[$slot][] = $widget->id();
            }
        }
    }

    /**
     * Legacy: sidebar → right, home → left+right.
     *
     * @param  list<string>  $slots
     * @return list<string>
     */
    public function normalizeSlots(array $slots): array
    {
        $out = [];
        foreach ($slots as $slot) {
            if ($slot === 'sidebar') {
                $out[] = 'right';
            } elseif ($slot === 'home') {
                $out[] = 'left';
                $out[] = 'right';
            } else {
                $out[] = $slot;
            }
        }

        return array_values(array_unique($out));
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
     * @return array<string, string>
     */
    public function slotLabels(): array
    {
        return [
            'left' => __('widgets.slot_left'),
            'right' => __('widgets.slot_right'),
            'footer' => __('widgets.slot_footer'),
            'dashboard' => __('widgets.slot_dashboard'),
        ];
    }

    /**
     * @return string[]
     */
    public function forSlot(string $slot): array
    {
        $slot = $this->normalizeSlots([$slot])[0] ?? $slot;

        return $this->slots[$slot] ?? [];
    }

    public function addSlot(string $slot): void
    {
        if (! isset($this->slots[$slot])) {
            $this->slots[$slot] = [];
        }
    }
}

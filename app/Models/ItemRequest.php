<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_REJECTED = 'rejected';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'user_id', 'craftable_item_id', 'custom_request',
        'max_price', 'desired_date', 'priority', 'quantity',
        'status', 'admin_notes',
    ];

    protected $casts = [
        'max_price' => 'decimal:2',
        'desired_date' => 'date',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function craftableItem(): BelongsTo
    {
        return $this->belongsTo(CraftableItem::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => __('crafting.status_pending'),
            self::STATUS_IN_PROGRESS => __('crafting.status_in_progress'),
            self::STATUS_DONE => __('crafting.status_done'),
            self::STATUS_REJECTED => __('crafting.status_rejected'),
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW => __('crafting.priority_low'),
            self::PRIORITY_NORMAL => __('crafting.priority_normal'),
            self::PRIORITY_HIGH => __('crafting.priority_high'),
        ];
    }
}

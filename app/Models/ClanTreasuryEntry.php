<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanTreasuryEntry extends Model
{
    protected $fillable = ['type', 'amount', 'clan_treasury_category_id', 'title', 'note', 'entry_date'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'entry_date' => 'date'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClanTreasuryCategory::class, 'clan_treasury_category_id');
    }
}

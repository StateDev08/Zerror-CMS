<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanMember extends Model
{
    protected $fillable = ['display_name', 'rank_id', 'position', 'avatar', 'visible', 'order', 'user_id'];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanBankItem extends Model
{
    protected $fillable = ['clan_bank_category_id', 'name', 'description', 'quantity', 'location', 'visible'];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'visible' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClanBankCategory::class, 'clan_bank_category_id');
    }
}

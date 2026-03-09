<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClanTreasuryCategory extends Model
{
    protected $fillable = ['name', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ClanTreasuryEntry::class, 'clan_treasury_category_id');
    }
}

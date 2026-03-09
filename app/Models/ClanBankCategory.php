<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClanBankCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClanBankItem::class, 'clan_bank_category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

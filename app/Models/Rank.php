<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function clanMembers(): HasMany
    {
        return $this->hasMany(ClanMember::class)->orderBy('order');
    }
}

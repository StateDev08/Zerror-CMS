<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CraftableItem extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'category', 'order', 'active'];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}

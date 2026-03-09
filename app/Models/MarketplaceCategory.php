<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(MarketplaceListing::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

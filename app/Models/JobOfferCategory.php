<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOfferCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOffer extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'description', 'location', 'employment_type', 'contact_email', 'published', 'expires_at'];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'expires_at' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobOfferCategory::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'position',
        'label',
        'link',
        'sort_order',
        'is_visible',
        'parent_id',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    /**
     * Resolved URL for the link (supports route names and raw URLs).
     */
    public function getResolvedUrlAttribute(): string
    {
        $link = trim($this->link);
        if (empty($link)) {
            return url('/');
        }
        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://') || str_starts_with($link, '//')) {
            return $link;
        }
        if (\Illuminate\Support\Facades\Route::has($link)) {
            return route($link);
        }
        if (str_starts_with($link, '/')) {
            return url($link);
        }
        return url('/' . $link);
    }
}

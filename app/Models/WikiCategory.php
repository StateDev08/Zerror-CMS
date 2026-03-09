<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WikiCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(WikiArticle::class, 'category_id')->orderBy('order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

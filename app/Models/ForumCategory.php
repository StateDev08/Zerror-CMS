<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    protected $table = 'forum_categories';

    protected $fillable = ['name', 'slug', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function forums(): HasMany
    {
        return $this->hasMany(ForumForum::class, 'category_id')->orderBy('order');
    }
}

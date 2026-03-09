<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClanDocumentCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClanDocument::class, 'clan_document_category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClanTeam extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'contact', 'visible', 'order'];

    protected function casts(): array
    {
        return ['visible' => 'boolean', 'order' => 'integer'];
    }

    public function members(): HasMany
    {
        return $this->hasMany(ClanTeamMember::class)->orderBy('order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

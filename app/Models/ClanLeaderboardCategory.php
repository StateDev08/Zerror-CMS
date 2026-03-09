<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClanLeaderboardCategory extends Model
{
    protected $fillable = ['name', 'slug', 'season', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ClanLeaderboardEntry::class, 'clan_leaderboard_category_id')->orderByDesc('score');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

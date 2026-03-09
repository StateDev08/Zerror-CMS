<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanLeaderboardEntry extends Model
{
    protected $fillable = ['clan_leaderboard_category_id', 'player_name', 'score', 'rank', 'order'];

    protected function casts(): array
    {
        return ['score' => 'integer', 'rank' => 'integer', 'order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClanLeaderboardCategory::class, 'clan_leaderboard_category_id');
    }
}

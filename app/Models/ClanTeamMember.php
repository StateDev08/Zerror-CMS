<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanTeamMember extends Model
{
    protected $fillable = ['clan_team_id', 'clan_member_id', 'display_name', 'role', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function clanTeam(): BelongsTo
    {
        return $this->belongsTo(ClanTeam::class);
    }

    public function clanMember(): BelongsTo
    {
        return $this->belongsTo(ClanMember::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClanAchievement extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'achieved_at', 'visible', 'order'];

    protected function casts(): array
    {
        return ['achieved_at' => 'date', 'visible' => 'boolean', 'order' => 'integer'];
    }
}

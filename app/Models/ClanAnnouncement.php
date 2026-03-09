<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClanAnnouncement extends Model
{
    protected $fillable = ['title', 'body', 'visible', 'visible_until', 'order'];

    protected function casts(): array
    {
        return ['visible' => 'boolean', 'visible_until' => 'datetime', 'order' => 'integer'];
    }
}

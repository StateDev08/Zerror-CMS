<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClanRule extends Model
{
    protected $fillable = ['title', 'content', 'order', 'visible'];

    protected function casts(): array
    {
        return ['order' => 'integer', 'visible' => 'boolean'];
    }
}

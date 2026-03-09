<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'url', 'logo', 'description', 'order'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['name', 'email', 'message', 'status', 'notes'];

    protected function casts(): array
    {
        return [];
    }
}

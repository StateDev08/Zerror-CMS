<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClanFeedback extends Model
{
    protected $table = 'clan_feedback';

    protected $fillable = ['author_name', 'author_email', 'message', 'status', 'admin_notes'];

    protected function casts(): array
    {
        return [];
    }
}

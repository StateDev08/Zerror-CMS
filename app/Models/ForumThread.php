<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    protected $table = 'forum_threads';

    protected $fillable = ['forum_id', 'user_id', 'title', 'pinned', 'locked'];

    protected function casts(): array
    {
        return ['pinned' => 'boolean', 'locked' => 'boolean'];
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(ForumForum::class, 'forum_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'thread_id');
    }
}

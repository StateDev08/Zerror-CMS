<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumForum extends Model
{
    protected $table = 'forum_forums';

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'order', 'read_rank_id', 'write_rank_id'];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'forum_id');
    }

    public function readRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'read_rank_id');
    }

    public function writeRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'write_rank_id');
    }
}

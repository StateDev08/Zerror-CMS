<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = ['category_id', 'name', 'file_path', 'version', 'download_count'];

    protected function casts(): array
    {
        return ['download_count' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DownloadCategory::class, 'category_id');
    }
}

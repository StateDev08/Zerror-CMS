<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanDocument extends Model
{
    protected $fillable = ['clan_document_category_id', 'title', 'content', 'file_path', 'visible', 'order'];

    protected function casts(): array
    {
        return ['visible' => 'boolean', 'order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClanDocumentCategory::class, 'clan_document_category_id');
    }

    /** Sichere Dateipfad-URL für Download (verhindert Path-Traversal). */
    public function getSafeFileUrlAttribute(): ?string
    {
        if (! $this->file_path || str_contains($this->file_path, '..')) {
            return null;
        }
        if (! str_starts_with($this->file_path, 'clan-documents/')) {
            return null;
        }
        return storage_asset($this->file_path);
    }
}

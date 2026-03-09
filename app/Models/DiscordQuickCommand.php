<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscordQuickCommand extends Model
{
    protected $fillable = ['name', 'description', 'response_text', 'created_by', 'is_public', 'use_count'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incrementUseCount(): void
    {
        $this->increment('use_count');
    }

    /** Name für Discord (nur Kleinbuchstaben, Unterstriche). */
    public static function normalizeName(string $name): string
    {
        return str_replace([' ', '-'], '_', strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $name)));
    }
}

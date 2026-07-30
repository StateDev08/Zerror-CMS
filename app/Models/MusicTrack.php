<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MusicTrack extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'file_path',
        'order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function publicUrl(): string
    {
        $path = ltrim((string) $this->file_path, '/');

        return $path !== '' ? storage_asset($path) : '';
    }
}

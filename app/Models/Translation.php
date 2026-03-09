<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'key', 'value'];

    public static function loadForLocale(string $locale): array
    {
        return static::where('locale', $locale)->pluck('value', 'key')->all();
    }
}

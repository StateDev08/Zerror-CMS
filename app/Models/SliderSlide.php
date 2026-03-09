<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderSlide extends Model
{
    protected $fillable = ['title', 'subtitle', 'image', 'link', 'order', 'active'];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'active' => 'boolean',
        ];
    }
}

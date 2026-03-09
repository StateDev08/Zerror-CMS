<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetInstance extends Model
{
    protected $fillable = ['slot', 'widget_key', 'order', 'config'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'order' => 'integer',
        ];
    }
}

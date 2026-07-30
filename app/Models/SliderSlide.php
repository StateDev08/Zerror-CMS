<?php

namespace App\Models;

use App\Support\SiteMedia;
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

    protected static function booted(): void
    {
        static::saved(function (SliderSlide $slide): void {
            if (! $slide->active) {
                return;
            }
            if (! SiteMedia::bannerEnabled() || ! SiteMedia::bannerConfigured()) {
                return;
            }
            SiteMedia::disableBannerForSlider();
            session()->flash('warning', __('settings.banner_slider_conflict_auto'));
        });
    }
}

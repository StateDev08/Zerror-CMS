<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\View;

class CookieBannerWidget implements WidgetContract
{
    public function id(): string
    {
        return 'cookie_banner';
    }

    public function title(): string
    {
        return __('widgets.cookie_banner_title', [], 'Cookie-Hinweis');
    }

    public function render(array $config = []): string
    {
        $config = array_merge(module_config('cookie_banner'), $config);
        return view('components.widgets.cookie-banner', $config)->render();
    }

    public function configSchema(): array
    {
        return [];
    }
}

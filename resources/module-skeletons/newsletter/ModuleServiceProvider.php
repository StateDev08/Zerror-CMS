<?php

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

if (! class_exists('ZerroNewsletterWidget', false)) {
    class ZerroNewsletterWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'newsletter';
        }

        public function title(): string
        {
            return __('widgets.newsletter_box_default_title');
        }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('newsletter'), $config);
            $views = base_path('modules/newsletter/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_newsletter', $views);
            }

            return view('mod_newsletter::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.newsletter_box_default_title'),
                'intro' => trim((string) ($cfg['intro'] ?? '')),
                'buttonText' => trim((string) ($cfg['button_text'] ?? '')) ?: __('widgets.newsletter_box_subscribe'),
                'showPageLink' => filter_var($cfg['show_page_link'] ?? true, FILTER_VALIDATE_BOOLEAN) && Route::has('newsletter.index'),
                'uniq' => Str::lower(Str::random(6)),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.newsletter_box_title'), 'default' => ''],
                'intro' => ['type' => 'textarea', 'label' => __('widgets.newsletter_intro'), 'default' => ''],
                'button_text' => ['type' => 'text', 'label' => __('widgets.newsletter_box_subscribe'), 'default' => ''],
                'show_page_link' => ['type' => 'boolean', 'label' => __('widgets.show_all_link'), 'default' => true],
            ];
        }
    }
}

if (! class_exists('ZerroNewsletterModuleServiceProvider', false)) {
    class ZerroNewsletterModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/newsletter/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_newsletter');
            }
            app(WidgetRegistry::class)->register(new ZerroNewsletterWidget(), ['left', 'right']);
        }
    }
}

return ZerroNewsletterModuleServiceProvider::class;

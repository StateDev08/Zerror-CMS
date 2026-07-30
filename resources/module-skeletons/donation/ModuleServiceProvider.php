<?php

use App\Support\SiteContent;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroDonationWidget', false)) {
    class ZerroDonationWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'donation';
        }

        public function title(): string
        {
            return __('widgets.donation_title');
        }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('donation'), $config);
            $url = trim((string) ($cfg['url'] ?? ''));
            if ($url === '') {
                $url = SiteContent::donationUrl();
            }
            $views = base_path('modules/donation/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_donation', $views);
            }

            return view('mod_donation::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.donation_title'),
                'intro' => trim((string) ($cfg['intro'] ?? '')) ?: __('widgets.donation_intro'),
                'buttonText' => trim((string) ($cfg['button_text'] ?? '')) ?: __('widgets.donation_button'),
                'url' => $url,
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'intro' => ['type' => 'textarea', 'label' => __('widgets.donation_intro'), 'default' => ''],
                'button_text' => ['type' => 'text', 'label' => __('widgets.donation_button_text'), 'default' => ''],
                'url' => ['type' => 'url', 'label' => __('widgets.donation_url'), 'default' => ''],
            ];
        }
    }
}

if (! class_exists('ZerroDonationModuleServiceProvider', false)) {
    class ZerroDonationModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/donation/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_donation');
            }
            app(WidgetRegistry::class)->register(new ZerroDonationWidget(), ['left', 'right']);
        }
    }
}

return ZerroDonationModuleServiceProvider::class;

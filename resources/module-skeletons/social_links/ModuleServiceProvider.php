<?php

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroSocialLinksWidget', false)) {
    class ZerroSocialLinksWidget implements WidgetContract
    {
        public function id(): string { return 'social_links'; }
        public function title(): string { return __('widgets.social_title'); }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('social_links'), $config);
            $map = [
                'discord' => [__('widgets.social_discord'), 'override_discord', 'social_discord'],
                'youtube' => [__('widgets.social_youtube'), 'override_youtube', 'social_youtube'],
                'twitch' => [__('widgets.social_twitch'), 'override_twitch', 'social_twitch'],
                'twitter' => [__('widgets.social_twitter'), 'override_twitter', 'social_twitter'],
                'instagram' => [__('widgets.social_instagram'), 'override_instagram', 'social_instagram'],
                'facebook' => [__('widgets.social_facebook'), 'override_facebook', 'social_facebook'],
            ];
            $links = [];
            foreach ($map as $row) {
                [$label, $overrideKey, $settingKey] = $row;
                $url = trim((string) ($cfg[$overrideKey] ?? ''));
                if ($url === '') {
                    $url = trim((string) setting($settingKey, ''));
                }
                if ($url !== '') {
                    $links[] = ['label' => $label, 'url' => $url];
                }
            }
            foreach (preg_split('/\r\n|\r|\n/', (string) ($cfg['custom_links'] ?? '')) as $line) {
                $line = trim($line);
                if ($line === '' || ! str_contains($line, '|')) {
                    continue;
                }
                [$label, $url] = array_map('trim', explode('|', $line, 2));
                if ($label !== '' && $url !== '') {
                    $links[] = ['label' => $label, 'url' => $url];
                }
            }
            $views = base_path('modules/social_links/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_social_links', $views);
            }

            return view('mod_social_links::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.social_title'),
                'links' => $links,
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'custom_links' => ['type' => 'textarea', 'label' => __('widgets.social_custom_links'), 'default' => ''],
            ];
        }
    }
}

if (! class_exists('ZerroSocialLinksModuleServiceProvider', false)) {
    class ZerroSocialLinksModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}
        public function boot(): void
        {
            $views = base_path('modules/social_links/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_social_links');
            }
            app(WidgetRegistry::class)->register(new ZerroSocialLinksWidget(), ['left', 'right']);
        }
    }
}

return ZerroSocialLinksModuleServiceProvider::class;
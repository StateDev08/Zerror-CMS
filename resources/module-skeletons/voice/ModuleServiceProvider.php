<?php

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroVoiceWidget', false)) {
    class ZerroVoiceWidget implements WidgetContract
    {
        public function id(): string { return 'voice'; }
        public function title(): string { return __('widgets.voice_title'); }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('voice'), $config);
            $views = base_path('modules/voice/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_voice', $views);
            }

            return view('mod_voice::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.voice_title'),
                'connectUrl' => trim((string) ($cfg['connect_url'] ?? '')),
                'hostLabel' => trim((string) ($cfg['host_label'] ?? '')),
                'buttonText' => trim((string) ($cfg['button_text'] ?? '')) ?: __('widgets.voice_connect'),
                'hint' => trim((string) ($cfg['hint'] ?? '')),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'connect_url' => ['type' => 'url', 'label' => __('widgets.voice_connect_url'), 'default' => ''],
                'host_label' => ['type' => 'text', 'label' => __('widgets.voice_host_label'), 'default' => ''],
                'button_text' => ['type' => 'text', 'label' => __('widgets.voice_button_text'), 'default' => ''],
                'hint' => ['type' => 'textarea', 'label' => __('widgets.voice_hint'), 'default' => ''],
            ];
        }
    }
}

if (! class_exists('ZerroVoiceModuleServiceProvider', false)) {
    class ZerroVoiceModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}
        public function boot(): void
        {
            $views = base_path('modules/voice/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_voice');
            }
            app(WidgetRegistry::class)->register(new ZerroVoiceWidget(), ['left', 'right']);
        }
    }
}

return ZerroVoiceModuleServiceProvider::class;
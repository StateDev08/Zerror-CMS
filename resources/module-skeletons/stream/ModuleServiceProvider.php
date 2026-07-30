<?php

use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroStreamWidget', false)) {
    class ZerroStreamWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'stream';
        }

        public function title(): string
        {
            return __('widgets.stream_title');
        }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('stream'), $config);
            $mode = strtolower(trim((string) ($cfg['mode'] ?? 'embed'))) === 'link' ? 'link' : 'embed';
            $platform = strtolower(trim((string) ($cfg['platform'] ?? 'twitch')));
            $raw = trim((string) ($cfg['channel_or_url'] ?? ''));
            $parent = trim((string) ($cfg['parent_domain'] ?? ''));
            if ($parent === '') {
                $parent = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
            }
            $embedUrl = null;
            $chatUrl = null;
            $watchUrl = null;

            if ($raw !== '') {
                if ($platform === 'youtube') {
                    $id = $raw;
                    if (preg_match('~(?:youtu\\.be/|v=|embed/)([A-Za-z0-9_-]{6,})~', $raw, $m)) {
                        $id = $m[1];
                    }
                    $embedUrl = 'https://www.youtube.com/embed/'.rawurlencode($id);
                    $watchUrl = 'https://www.youtube.com/watch?v='.rawurlencode($id);
                } else {
                    $channel = $raw;
                    if (preg_match('~twitch\\.tv/([A-Za-z0-9_]+)~', $raw, $m)) {
                        $channel = $m[1];
                    }
                    $channel = ltrim($channel, '@');
                    $embedUrl = 'https://player.twitch.tv/?channel='.rawurlencode($channel).'&parent='.rawurlencode($parent).'&muted=true';
                    $watchUrl = 'https://www.twitch.tv/'.rawurlencode($channel);
                    if (filter_var($cfg['show_chat'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                        $chatUrl = 'https://www.twitch.tv/embed/'.rawurlencode($channel).'/chat?parent='.rawurlencode($parent);
                    }
                }
            }

            $views = base_path('modules/stream/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_stream', $views);
            }

            return view('mod_stream::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.stream_title'),
                'mode' => $mode,
                'embedUrl' => $embedUrl,
                'chatUrl' => $chatUrl,
                'watchUrl' => $watchUrl,
                'buttonText' => trim((string) ($cfg['button_text'] ?? '')) ?: __('widgets.stream_button'),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'mode' => ['type' => 'text', 'label' => __('widgets.stream_mode'), 'default' => 'embed'],
                'platform' => ['type' => 'text', 'label' => __('widgets.stream_platform'), 'default' => 'twitch'],
                'channel_or_url' => ['type' => 'text', 'label' => __('widgets.stream_channel'), 'default' => ''],
                'parent_domain' => ['type' => 'text', 'label' => __('widgets.stream_parent'), 'default' => ''],
                'show_chat' => ['type' => 'boolean', 'label' => __('widgets.stream_show_chat'), 'default' => false],
                'button_text' => ['type' => 'text', 'label' => __('widgets.stream_button'), 'default' => ''],
            ];
        }
    }
}

if (! class_exists('ZerroStreamModuleServiceProvider', false)) {
    class ZerroStreamModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/stream/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_stream');
            }
            app(WidgetRegistry::class)->register(new ZerroStreamWidget(), ['left', 'right']);
        }
    }
}

return ZerroStreamModuleServiceProvider::class;

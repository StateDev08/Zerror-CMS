<?php

use App\Models\MusicTrack;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroMusicPlayerWidget', false)) {
    class ZerroMusicPlayerWidget implements WidgetContract
    {
        public function id(): string
        {
            return 'music_player';
        }

        public function title(): string
        {
            return __('widgets.music_player_title');
        }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('music_player'), $config);
            $views = base_path('modules/music_player/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_music_player', $views);
            }

            $tracks = [];
            if (Schema::hasTable('music_tracks')) {
                $tracks = MusicTrack::query()
                    ->active()
                    ->ordered()
                    ->get()
                    ->map(fn (MusicTrack $t) => [
                        'id' => $t->id,
                        'title' => $t->title,
                        'artist' => (string) ($t->artist ?? ''),
                        'url' => $t->publicUrl(),
                    ])
                    ->filter(fn (array $t) => $t['url'] !== '')
                    ->values()
                    ->all();
            }

            $volume = (int) ($cfg['volume'] ?? 70);
            $volume = max(0, min(100, $volume));

            return view('mod_music_player::widget', [
                'title' => trim((string) ($cfg['title'] ?? '')) ?: __('widgets.music_player_title'),
                'tracks' => $tracks,
                'autoplay' => filter_var($cfg['autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'playlistLoop' => filter_var($cfg['loop'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'volume' => $volume,
                'showControls' => filter_var($cfg['show_controls'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'uniq' => 'mp-'.substr(sha1(json_encode($tracks).'|'.microtime(true)), 0, 8),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.title_override'), 'default' => ''],
                'autoplay' => ['type' => 'boolean', 'label' => __('widgets.music_player_autoplay'), 'default' => false],
                'loop' => ['type' => 'boolean', 'label' => __('widgets.music_player_loop'), 'default' => true],
                'volume' => ['type' => 'number', 'label' => __('widgets.music_player_volume'), 'default' => 70],
                'show_controls' => ['type' => 'boolean', 'label' => __('widgets.music_player_show_controls'), 'default' => true],
            ];
        }
    }
}

if (! class_exists('ZerroMusicPlayerModuleServiceProvider', false)) {
    class ZerroMusicPlayerModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/music_player/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_music_player');
            }
            app(WidgetRegistry::class)->register(new ZerroMusicPlayerWidget(), ['left', 'right']);
        }
    }
}

return ZerroMusicPlayerModuleServiceProvider::class;

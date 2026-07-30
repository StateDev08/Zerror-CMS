<?php

use App\Support\DiscordWidgetApi;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroDiscordWidget', false)) {
    class ZerroDiscordWidget implements WidgetContract
    {
        public function id(): string { return 'discord'; }
        public function title(): string { return __('widgets.discord_title'); }

        public function render(array $config = []): string
        {
            $cfg = array_merge(module_config('discord'), $config);
            $invite = trim((string) ($cfg['invite_url'] ?? ''));
            if ($invite === '') {
                $invite = trim((string) setting('discord_invite_url', setting('social_discord', '')));
            }
            $guildId = trim((string) ($cfg['guild_id'] ?? ''));
            $cache = max(15, (int) ($cfg['cache_seconds'] ?? 60));
            $data = DiscordWidgetApi::fetch($guildId, $cache, $invite !== '' ? $invite : null);
            if ($invite === '' && ! empty($data['invite'])) {
                $invite = (string) $data['invite'];
            }
            $displayName = trim((string) ($cfg['title'] ?? '')) ?: ($data['name'] ?? __('widgets.discord_title'));
            $views = base_path('modules/discord/views');
            if (is_dir($views)) {
                view()->addNamespace('mod_discord', $views);
            }

            return view('mod_discord::widget', [
                'data' => $data,
                'invite' => $invite !== '' ? $invite : null,
                'displayName' => $displayName,
                'tagline' => trim((string) ($cfg['tagline'] ?? '')),
                'buttonText' => trim((string) ($cfg['button_text'] ?? '')) ?: __('widgets.discord_join'),
                'showOnline' => filter_var($cfg['show_online_count'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'showMembers' => filter_var($cfg['show_members'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'memberLimit' => max(1, min(20, (int) ($cfg['member_limit'] ?? 8))),
                'showChannels' => filter_var($cfg['show_channels'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ])->render();
        }

        public function configSchema(): array
        {
            return [
                'title' => ['type' => 'text', 'label' => __('widgets.discord_widget_title'), 'default' => ''],
                'tagline' => ['type' => 'text', 'label' => __('widgets.discord_tagline'), 'default' => ''],
                'guild_id' => ['type' => 'text', 'label' => __('widgets.discord_guild_id'), 'default' => ''],
                'invite_url' => ['type' => 'url', 'label' => __('widgets.discord_invite_url'), 'default' => ''],
                'button_text' => ['type' => 'text', 'label' => __('widgets.discord_button_text'), 'default' => ''],
                'show_online_count' => ['type' => 'boolean', 'label' => __('widgets.discord_online_count', ['count' => 'n']), 'default' => true],
                'show_members' => ['type' => 'boolean', 'label' => __('widgets.discord_show_members'), 'default' => true],
                'member_limit' => ['type' => 'number', 'label' => __('widgets.discord_member_limit'), 'default' => 8],
                'show_channels' => ['type' => 'boolean', 'label' => __('widgets.discord_show_channels'), 'default' => true],
            ];
        }
    }
}

if (! class_exists('ZerroDiscordModuleServiceProvider', false)) {
    class ZerroDiscordModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}
        public function boot(): void
        {
            $views = base_path('modules/discord/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_discord');
            }
            app(WidgetRegistry::class)->register(new ZerroDiscordWidget(), ['left', 'right']);
        }
    }
}

return ZerroDiscordModuleServiceProvider::class;
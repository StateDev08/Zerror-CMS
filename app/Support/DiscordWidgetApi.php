<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DiscordWidgetApi
{
    /**
     * @return array{
     *     ok: bool,
     *     name: string|null,
     *     invite: string|null,
     *     presence_count: int,
     *     members: list<array{username: string, status: string, avatar: string|null}>,
     *     channels: list<array{id: string, name: string, position: int}>,
     *     error: string|null,
     *     guild_id: string|null
     * }
     */
    public static function fetch(string $guildId, int $cacheSeconds = 60, ?string $inviteUrl = null): array
    {
        $guildId = self::normalizeGuildId($guildId);
        $inviteUrl = $inviteUrl !== null ? trim($inviteUrl) : null;

        if ($guildId === '' && $inviteUrl) {
            $resolved = self::resolveGuildIdFromInvite($inviteUrl, $cacheSeconds);
            if ($resolved['guild_id']) {
                $guildId = $resolved['guild_id'];
            }
        }

        $empty = self::emptyResult($guildId === '' ? 'missing_guild' : null);
        if ($guildId === '') {
            return $empty;
        }

        $key = 'discord_widget:v2:'.$guildId;

        /** @var array $cached */
        $cached = Cache::remember($key, max(15, $cacheSeconds), function () use ($guildId) {
            return self::fetchWidgetJson($guildId);
        });

        if (! ($cached['ok'] ?? false) && ($cached['error'] ?? '') === 'http_403') {
            $cached['error'] = 'widget_disabled';
        }

        if ($inviteUrl && empty($cached['invite'])) {
            $cached['invite'] = $inviteUrl;
        }

        $cached['guild_id'] = $guildId;

        return $cached;
    }

    public static function normalizeGuildId(string $guildId): string
    {
        return preg_replace('/\D+/', '', $guildId) ?? '';
    }

    /**
     * Invite-Code aus URL oder Rohcode extrahieren.
     */
    public static function inviteCode(?string $inviteUrl): ?string
    {
        $inviteUrl = trim((string) $inviteUrl);
        if ($inviteUrl === '') {
            return null;
        }

        if (preg_match('#(?:discord(?:\.gg|app\.com/invite|\\.com/invite)/)([a-zA-Z0-9-]+)#i', $inviteUrl, $m)) {
            return $m[1];
        }

        if (preg_match('/^[a-zA-Z0-9-]{2,32}$/', $inviteUrl)) {
            return $inviteUrl;
        }

        return null;
    }

    /**
     * @return array{guild_id: string|null, name: string|null, presence_count: int, error: string|null}
     */
    public static function resolveGuildIdFromInvite(string $inviteUrl, int $cacheSeconds = 60): array
    {
        $code = self::inviteCode($inviteUrl);
        $empty = ['guild_id' => null, 'name' => null, 'presence_count' => 0, 'error' => 'invalid_invite'];
        if (! $code) {
            return $empty;
        }

        $key = 'discord_invite_resolve:'.$code;

        return Cache::remember($key, max(30, $cacheSeconds), function () use ($code, $empty) {
            try {
                $response = Http::timeout(4)
                    ->acceptJson()
                    ->get('https://discord.com/api/v10/invites/'.$code, [
                        'with_counts' => 'true',
                        'with_expiration' => 'true',
                    ]);

                if (! $response->successful()) {
                    $empty['error'] = 'invite_http_'.$response->status();

                    return $empty;
                }

                $data = $response->json();
                if (! is_array($data) || empty($data['guild']['id'])) {
                    $empty['error'] = 'invite_no_guild';

                    return $empty;
                }

                return [
                    'guild_id' => (string) $data['guild']['id'],
                    'name' => isset($data['guild']['name']) ? (string) $data['guild']['name'] : null,
                    'presence_count' => (int) ($data['approximate_presence_count'] ?? 0),
                    'error' => null,
                ];
            } catch (\Throwable $e) {
                report($e);
                $empty['error'] = 'invite_exception';

                return $empty;
            }
        });
    }

    /**
     * @return array{
     *     ok: bool,
     *     name: string|null,
     *     invite: string|null,
     *     presence_count: int,
     *     members: list<array{username: string, status: string, avatar: string|null}>,
     *     channels: list<array{id: string, name: string, position: int}>,
     *     error: string|null,
     *     guild_id: string|null
     * }
     */
    protected static function fetchWidgetJson(string $guildId): array
    {
        $empty = self::emptyResult(null);
        $empty['guild_id'] = $guildId;

        try {
            $response = Http::timeout(4)
                ->acceptJson()
                ->get('https://discord.com/api/guilds/'.$guildId.'/widget.json');

            if (! $response->successful()) {
                $empty['error'] = 'http_'.$response->status();

                return $empty;
            }

            $data = $response->json();
            if (! is_array($data)) {
                $empty['error'] = 'invalid_json';

                return $empty;
            }

            $members = [];
            foreach (($data['members'] ?? []) as $member) {
                if (! is_array($member)) {
                    continue;
                }
                $members[] = [
                    'username' => (string) ($member['username'] ?? 'User'),
                    'status' => (string) ($member['status'] ?? 'online'),
                    'avatar' => isset($member['avatar_url']) ? (string) $member['avatar_url'] : null,
                ];
            }

            $channels = [];
            foreach (($data['channels'] ?? []) as $channel) {
                if (! is_array($channel)) {
                    continue;
                }
                $name = trim((string) ($channel['name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $channels[] = [
                    'id' => (string) ($channel['id'] ?? ''),
                    'name' => $name,
                    'position' => (int) ($channel['position'] ?? 0),
                ];
            }
            usort($channels, fn ($a, $b) => $a['position'] <=> $b['position']);

            return [
                'ok' => true,
                'name' => isset($data['name']) ? (string) $data['name'] : null,
                'invite' => isset($data['instant_invite']) ? (string) $data['instant_invite'] : null,
                'presence_count' => (int) ($data['presence_count'] ?? count($members)),
                'members' => $members,
                'channels' => $channels,
                'error' => null,
                'guild_id' => $guildId,
            ];
        } catch (\Throwable $e) {
            report($e);
            $empty['error'] = 'exception';

            return $empty;
        }
    }

    /**
     * @return array{
     *     ok: bool,
     *     name: string|null,
     *     invite: string|null,
     *     presence_count: int,
     *     members: list<array{username: string, status: string, avatar: string|null}>,
     *     channels: list<array{id: string, name: string, position: int}>,
     *     error: string|null,
     *     guild_id: string|null
     * }
     */
    protected static function emptyResult(?string $error): array
    {
        return [
            'ok' => false,
            'name' => null,
            'invite' => null,
            'presence_count' => 0,
            'members' => [],
            'channels' => [],
            'error' => $error,
            'guild_id' => null,
        ];
    }
}

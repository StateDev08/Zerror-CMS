<?php

namespace App\Support;

use App\Support\GameServerQuery;
use App\Support\ServerStatusServers;

class ServerStatusService
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function resolve(?array $config = null): array
    {
        $merged = array_merge(module_config('server_status'), $config ?? []);
        $servers = ServerStatusServers::fromConfig($merged);
        $cacheSeconds = (int) ($merged['cache_seconds'] ?? 30);
        $timeout = (float) ($merged['timeout'] ?? 2.5);

        $results = [];
        foreach ($servers as $server) {
            $status = GameServerQuery::query(
                $server['host'],
                $server['port'],
                $server['type'],
                $timeout,
                max(0, $cacheSeconds),
                $server['query_port']
            );

            // Spielname: Config hat Vorrang, sonst Query-Ergebnis
            if (empty($status['game']) && ! empty($server['game'])) {
                $status['game'] = $server['game'];
            }

            $results[] = [
                'label' => $server['label'],
                'host' => $server['host'],
                'port' => $server['port'],
                'query_port' => $server['query_port'],
                'type' => $server['type'],
                'game' => $server['game'] ?: ($status['game'] ?? 'Game'),
                'mod_type' => $server['mod_type'] ?? 'vanilla',
                'connect' => $server['host'].':'.$server['port'],
                'banner' => self::resolveBannerUrl($server['banner'] ?? null),
                'status' => $status,
            ];
        }

        return $results;
    }

    protected static function resolveBannerUrl(?string $banner): ?string
    {
        $banner = trim((string) $banner);
        if ($banner === '') {
            return null;
        }
        if (str_starts_with($banner, 'http://') || str_starts_with($banner, 'https://')) {
            return $banner;
        }

        return storage_asset($banner);
    }
}

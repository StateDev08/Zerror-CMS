<?php

namespace App\Support;

class ServerStatusServers
{
    /**
     * Format (eine Zeile pro Server):
     * Name|Host|Port|QueryPort|Typ|Spiel|BannerURL
     *
     * QueryPort leer = gleicher Port wie Port.
     * Typ: auto|minecraft|bedrock|source|tcp
     *
     * @param  array<string, mixed>  $config
     * @return list<array{
     *     label: string,
     *     host: string,
     *     port: int,
     *     query_port: int,
     *     type: string,
     *     game: string,
     *     banner: string|null
     * }>
     */
    public static function fromConfig(array $config): array
    {
        $servers = [];

        if (! empty($config['servers']) && is_string($config['servers'])) {
            $servers = array_merge($servers, self::parseList((string) $config['servers']));
        } elseif (! empty($config['servers']) && is_array($config['servers'])) {
            foreach ($config['servers'] as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $parsed = self::normalizeRow($row);
                if ($parsed !== null) {
                    $servers[] = $parsed;
                }
            }
        }

        if ($servers === [] && ! empty($config['host'])) {
            $single = self::normalizeRow([
                'label' => $config['label'] ?? 'Server',
                'host' => $config['host'],
                'port' => $config['port'] ?? 25565,
                'query_port' => $config['query_port'] ?? ($config['port'] ?? 25565),
                'type' => $config['query_type'] ?? $config['type'] ?? 'auto',
                'game' => $config['game'] ?? '',
                'banner' => $config['banner'] ?? '',
            ]);
            if ($single !== null) {
                $servers[] = $single;
            }
        }

        return $servers;
    }

    /**
     * @return list<array{label: string, host: string, port: int, query_port: int, type: string, game: string, banner: string|null}>
     */
    public static function parseList(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        if ($raw[0] === '[') {
            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                return [];
            }
            $out = [];
            foreach ($decoded as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $parsed = self::normalizeRow($row);
                if ($parsed !== null) {
                    $out[] = $parsed;
                }
            }

            return $out;
        }

        $out = [];
        foreach (preg_split('/\R/u', $raw) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 3) {
                continue;
            }

            // Neu: Name|Host|Port|QueryPort|Typ|Spiel|Banner
            // Alt:  Name|Host|Port|Typ
            $row = [
                'label' => $parts[0],
                'host' => $parts[1],
                'port' => $parts[2],
            ];

            $fourth = $parts[3] ?? '';
            if ($fourth === '' || is_numeric($fourth)) {
                $row['query_port'] = $fourth !== '' ? $fourth : $parts[2];
                $row['type'] = $parts[4] ?? 'auto';
                $row['game'] = $parts[5] ?? '';
                $row['banner'] = $parts[6] ?? '';
            } else {
                // Legacy ohne Query-Port: Name|Host|Port|Typ
                $row['query_port'] = $parts[2];
                $row['type'] = $fourth;
                $row['game'] = $parts[4] ?? '';
                $row['banner'] = $parts[5] ?? '';
            }

            $parsed = self::normalizeRow($row);
            if ($parsed !== null) {
                $out[] = $parsed;
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{label: string, host: string, port: int, query_port: int, type: string, game: string, banner: string|null}|null
     */
    protected static function normalizeRow(array $row): ?array
    {
        $host = trim((string) ($row['host'] ?? $row['ip'] ?? ''));
        if ($host === '') {
            return null;
        }

        $label = trim((string) ($row['label'] ?? $row['name'] ?? $host));
        $port = self::clampPort((int) ($row['port'] ?? 25565), 25565);
        $queryPortRaw = $row['query_port'] ?? $row['queryPort'] ?? null;
        $queryPort = ($queryPortRaw === null || $queryPortRaw === '')
            ? $port
            : self::clampPort((int) $queryPortRaw, $port);

        $type = strtolower(trim((string) ($row['type'] ?? $row['query_type'] ?? 'auto')));
        if ($type === '') {
            $type = 'auto';
        }

        $game = trim((string) ($row['game'] ?? $row['game_name'] ?? ''));
        if ($game === '') {
            $game = match ($type) {
                'minecraft', 'mc', 'java' => 'Minecraft',
                'bedrock', 'mcpe' => 'Minecraft Bedrock',
                'source', 'a2s', 'steam' => 'Source',
                'tcp' => 'Server',
                default => 'Game',
            };
        }

        $banner = trim((string) ($row['banner'] ?? $row['banner_url'] ?? ''));
        $modType = strtolower(trim((string) ($row['mod_type'] ?? $row['edition'] ?? 'vanilla')));
        if (! in_array($modType, ['vanilla', 'modded'], true)) {
            $modType = 'vanilla';
        }

        return [
            'label' => $label !== '' ? $label : $host,
            'host' => $host,
            'port' => $port,
            'query_port' => $queryPort,
            'type' => $type,
            'game' => $game,
            'mod_type' => $modType,
            'banner' => $banner !== '' ? $banner : null,
        ];
    }

    protected static function clampPort(int $port, int $fallback): int
    {
        return ($port >= 1 && $port <= 65535) ? $port : $fallback;
    }
}

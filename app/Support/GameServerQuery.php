<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class GameServerQuery
{
    /**
     * @return array{
     *     online: bool,
     *     latency_ms: int|null,
     *     players: int|null,
     *     max_players: int|null,
     *     version: string|null,
     *     motd: string|null,
     *     map: string|null,
     *     game: string|null,
     *     players_sample: list<string>,
     *     address: string,
     *     type: string,
     *     error: string|null
     * }
     */
    public static function query(string $host, int $port, string $type = 'auto', float $timeout = 2.5, ?int $cacheSeconds = 30, ?int $queryPort = null): array
    {
        $host = trim($host);
        $type = strtolower(trim($type ?: 'auto'));
        $queryPort = $queryPort && $queryPort > 0 ? $queryPort : $port;
        $address = $host.':'.$port;
        $empty = [
            'online' => false,
            'latency_ms' => null,
            'players' => null,
            'max_players' => null,
            'version' => null,
            'motd' => null,
            'map' => null,
            'game' => null,
            'players_sample' => [],
            'address' => $address,
            'connect_port' => $port,
            'query_port' => $queryPort,
            'type' => $type,
            'error' => null,
        ];

        if ($host === '' || $port < 1) {
            $empty['error'] = 'invalid';

            return $empty;
        }

        $runner = function () use ($host, $port, $queryPort, $type, $timeout, $empty) {
            return match ($type) {
                'minecraft', 'mc', 'java' => self::queryMinecraftJava($host, $queryPort, $timeout, $empty),
                'bedrock', 'mcpe' => self::queryMinecraftBedrock($host, $queryPort, $timeout, $empty),
                'source', 'a2s', 'steam' => self::querySource($host, $queryPort, $timeout, $empty),
                'tcp' => self::queryTcp($host, $port, $timeout, $empty),
                default => self::queryAuto($host, $port, $queryPort, $timeout, $empty),
            };
        };

        if ($cacheSeconds !== null && $cacheSeconds > 0) {
            $key = 'game_server_query:'.md5($host.'|'.$port.'|'.$queryPort.'|'.$type);

            return Cache::remember($key, $cacheSeconds, $runner);
        }

        return $runner();
    }

    /**
     * @param  array<string, mixed>  $empty
     * @return array<string, mixed>
     */
    protected static function queryAuto(string $host, int $port, int $queryPort, float $timeout, array $empty): array
    {
        $mc = self::queryMinecraftJava($host, $queryPort, $timeout, $empty);
        if ($mc['online']) {
            $mc['type'] = 'minecraft';

            return $mc;
        }

        $tcp = self::queryTcp($host, $port, $timeout, $empty);
        $tcp['type'] = 'tcp';

        return $tcp;
    }

    /**
     * @param  array<string, mixed>  $empty
     * @return array<string, mixed>
     */
    protected static function queryTcp(string $host, int $port, float $timeout, array $empty): array
    {
        $start = microtime(true);
        $errno = 0;
        $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($fp === false) {
            $empty['error'] = $errstr ?: 'offline';

            return $empty;
        }
        $latency = (int) round((microtime(true) - $start) * 1000);
        fclose($fp);
        $empty['online'] = true;
        $empty['latency_ms'] = $latency;
        $empty['type'] = 'tcp';
        $empty['game'] = 'TCP';

        return $empty;
    }

    /**
     * Minecraft Java Server List Ping (1.7+).
     *
     * @param  array<string, mixed>  $empty
     * @return array<string, mixed>
     */
    protected static function queryMinecraftJava(string $host, int $port, float $timeout, array $empty): array
    {
        $errno = 0;
        $errstr = '';
        $start = microtime(true);
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($fp === false) {
            $empty['error'] = $errstr ?: 'offline';
            $empty['type'] = 'minecraft';

            return $empty;
        }
        stream_set_timeout($fp, (int) max(1, ceil($timeout)));

        try {
            $handshake = self::minecraftPackData(
                self::minecraftPackVarInt(0)
                .self::minecraftPackVarInt(765)
                .self::minecraftPackString($host)
                .pack('n', $port)
                .self::minecraftPackVarInt(1)
            );
            fwrite($fp, $handshake);
            fwrite($fp, self::minecraftPackData(self::minecraftPackVarInt(0)));

            $length = self::minecraftReadVarInt($fp);
            if ($length < 1 || $length > 65535) {
                throw new \RuntimeException('bad length');
            }
            $buffer = self::minecraftReadExact($fp, $length);
            $offset = 0;
            $packetId = self::minecraftUnpackVarInt($buffer, $offset);
            if ($packetId !== 0) {
                throw new \RuntimeException('bad packet');
            }
            $jsonLen = self::minecraftUnpackVarInt($buffer, $offset);
            $json = substr($buffer, $offset, $jsonLen);
            $data = json_decode($json, true);
            if (! is_array($data)) {
                throw new \RuntimeException('bad json');
            }

            $latency = (int) round((microtime(true) - $start) * 1000);
            $motd = self::extractMotd($data['description'] ?? null);
            $sample = [];
            foreach (($data['players']['sample'] ?? []) as $player) {
                if (is_array($player) && ! empty($player['name'])) {
                    $sample[] = (string) $player['name'];
                }
            }

            return [
                'online' => true,
                'latency_ms' => $latency,
                'players' => isset($data['players']['online']) ? (int) $data['players']['online'] : null,
                'max_players' => isset($data['players']['max']) ? (int) $data['players']['max'] : null,
                'version' => isset($data['version']['name']) ? (string) $data['version']['name'] : null,
                'motd' => $motd,
                'map' => null,
                'game' => 'Minecraft',
                'players_sample' => $sample,
                'address' => $empty['address'],
                'connect_port' => $empty['connect_port'] ?? null,
                'query_port' => $empty['query_port'] ?? null,
                'type' => 'minecraft',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            $empty['error'] = $e->getMessage();
            $empty['type'] = 'minecraft';

            return $empty;
        } finally {
            fclose($fp);
        }
    }

    /**
     * Minecraft Bedrock unconnected ping.
     *
     * @param  array<string, mixed>  $empty
     * @return array<string, mixed>
     */
    protected static function queryMinecraftBedrock(string $host, int $port, float $timeout, array $empty): array
    {
        $empty['type'] = 'bedrock';
        $socket = @stream_socket_client('udp://'.$host.':'.$port, $errno, $errstr, $timeout);
        if ($socket === false) {
            $empty['error'] = $errstr ?: 'offline';

            return $empty;
        }
        stream_set_timeout($socket, (int) max(1, ceil($timeout)));
        $start = microtime(true);
        $guid = random_bytes(8);
        $time = pack('NN', 0, (int) (microtime(true) * 1000) & 0xFFFFFFFF);
        // RakNet Unconnected Ping
        $ping = "\x01".$time.$guid."\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78".$time;
        fwrite($socket, $ping);
        $response = fread($socket, 2048);
        fclose($socket);
        if ($response === false || $response === '' || ($response[0] ?? '') !== "\x1c") {
            $empty['error'] = 'no response';

            return $empty;
        }
        $latency = (int) round((microtime(true) - $start) * 1000);
        // Payload after header often contains ; separated server info
        $payload = substr($response, 35);
        $parts = explode(';', $payload);
        // MOTD;protocol;version;players;max;serverId;map;gamemode;...
        $motd = $parts[1] ?? ($parts[0] ?? null);
        $version = $parts[3] ?? null;
        $players = isset($parts[4]) && is_numeric($parts[4]) ? (int) $parts[4] : null;
        $max = isset($parts[5]) && is_numeric($parts[5]) ? (int) $parts[5] : null;
        $map = $parts[7] ?? null;

        return [
            'online' => true,
            'latency_ms' => $latency,
            'players' => $players,
            'max_players' => $max,
            'version' => $version,
            'motd' => is_string($motd) ? trim(strip_tags($motd)) : null,
            'map' => is_string($map) ? $map : null,
            'game' => 'Minecraft Bedrock',
            'players_sample' => [],
            'address' => $empty['address'],
            'connect_port' => $empty['connect_port'] ?? null,
            'query_port' => $empty['query_port'] ?? null,
            'type' => 'bedrock',
            'error' => null,
        ];
    }

    /**
     * Source Engine A2S_INFO (7 Days, CS, …).
     *
     * @param  array<string, mixed>  $empty
     * @return array<string, mixed>
     */
    protected static function querySource(string $host, int $port, float $timeout, array $empty): array
    {
        $empty['type'] = 'source';
        $socket = @stream_socket_client('udp://'.$host.':'.$port, $errno, $errstr, $timeout);
        if ($socket === false) {
            $empty['error'] = $errstr ?: 'offline';

            return $empty;
        }
        stream_set_timeout($socket, (int) max(1, ceil($timeout)));
        $start = microtime(true);
        // A2S_INFO
        fwrite($socket, "\xFF\xFF\xFF\xFFTSource Engine Query\x00");
        $response = fread($socket, 4096);
        if ($response !== false && strlen($response) >= 5 && $response[4] === 'A') {
            // Challenge response – resend with challenge
            $challenge = substr($response, 5, 4);
            fwrite($socket, "\xFF\xFF\xFF\xFFTSource Engine Query\x00".$challenge);
            $response = fread($socket, 4096);
        }
        fclose($socket);
        if ($response === false || strlen($response) < 6 || $response[4] !== 'I') {
            $empty['error'] = 'no response';

            return $empty;
        }
        $latency = (int) round((microtime(true) - $start) * 1000);
        $offset = 6; // skip header + type + protocol
        $name = self::readNullString($response, $offset);
        $map = self::readNullString($response, $offset);
        $folder = self::readNullString($response, $offset);
        $game = self::readNullString($response, $offset);
        $offset += 2; // app id
        $players = ord($response[$offset] ?? "\x00");
        $max = ord($response[$offset + 1] ?? "\x00");
        $bots = ord($response[$offset + 2] ?? "\x00");

        return [
            'online' => true,
            'latency_ms' => $latency,
            'players' => max(0, $players - $bots),
            'max_players' => $max,
            'version' => null,
            'motd' => $name !== '' ? $name : null,
            'map' => $map !== '' ? $map : null,
            'game' => $game !== '' ? $game : ($folder !== '' ? $folder : 'Source'),
            'players_sample' => [],
            'address' => $empty['address'],
            'connect_port' => $empty['connect_port'] ?? null,
            'query_port' => $empty['query_port'] ?? null,
            'type' => 'source',
            'error' => null,
        ];
    }

    protected static function readNullString(string $buffer, int &$offset): string
    {
        $end = strpos($buffer, "\x00", $offset);
        if ($end === false) {
            $str = substr($buffer, $offset);
            $offset = strlen($buffer);

            return $str;
        }
        $str = substr($buffer, $offset, $end - $offset);
        $offset = $end + 1;

        return $str;
    }

    protected static function extractMotd(mixed $description): ?string
    {
        if (is_string($description)) {
            return trim(preg_replace('/§./u', '', $description) ?? $description);
        }
        if (! is_array($description)) {
            return null;
        }
        $text = (string) ($description['text'] ?? '');
        foreach ($description['extra'] ?? [] as $part) {
            if (is_array($part)) {
                $text .= (string) ($part['text'] ?? '');
            } elseif (is_string($part)) {
                $text .= $part;
            }
        }
        $text = preg_replace('/§./u', '', $text) ?? $text;

        return trim($text) !== '' ? trim($text) : null;
    }

    protected static function minecraftPackVarInt(int $value): string
    {
        $out = '';
        do {
            $temp = $value & 0x7F;
            $value >>= 7;
            if ($value !== 0) {
                $temp |= 0x80;
            }
            $out .= chr($temp);
        } while ($value !== 0);

        return $out;
    }

    protected static function minecraftPackString(string $value): string
    {
        return self::minecraftPackVarInt(strlen($value)).$value;
    }

    protected static function minecraftPackData(string $data): string
    {
        return self::minecraftPackVarInt(strlen($data)).$data;
    }

    /**
     * @param  resource  $fp
     */
    protected static function minecraftReadVarInt($fp): int
    {
        $numRead = 0;
        $result = 0;
        do {
            $byte = fread($fp, 1);
            if ($byte === false || $byte === '') {
                throw new \RuntimeException('eof');
            }
            $value = ord($byte);
            $result |= ($value & 0x7F) << (7 * $numRead);
            $numRead++;
            if ($numRead > 5) {
                throw new \RuntimeException('varint too big');
            }
        } while (($value & 0x80) !== 0);

        return $result;
    }

    protected static function minecraftUnpackVarInt(string $buffer, int &$offset): int
    {
        $numRead = 0;
        $result = 0;
        do {
            if (! isset($buffer[$offset])) {
                throw new \RuntimeException('eof');
            }
            $value = ord($buffer[$offset]);
            $offset++;
            $result |= ($value & 0x7F) << (7 * $numRead);
            $numRead++;
            if ($numRead > 5) {
                throw new \RuntimeException('varint too big');
            }
        } while (($value & 0x80) !== 0);

        return $result;
    }

    /**
     * @param  resource  $fp
     */
    protected static function minecraftReadExact($fp, int $length): string
    {
        $data = '';
        while (strlen($data) < $length) {
            $chunk = fread($fp, $length - strlen($data));
            if ($chunk === false || $chunk === '') {
                throw new \RuntimeException('eof');
            }
            $data .= $chunk;
        }

        return $data;
    }
}

<?php

namespace App\Filament\Pages;

use App\Support\ServerStatusServers;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class GameServersPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;
    use WithFileUploads;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-server-stack';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?string $navigationLabel = 'Gameserver';

    protected static ?string $title = 'Gameserver';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.game-servers';

    /** @var list<array{label: string, host: string, port: int|string, query_port: int|string, type: string, game: string, banner: string}> */
    public array $servers = [];

    /** @var array<int, TemporaryUploadedFile|null> */
    public array $bannerUploads = [];

    public string $page_title = 'Server-Status';

    public string $page_intro = '';

    public string $widget_title = 'Server-Status';

    public int|string $cache_seconds = 30;

    public float|string $timeout = 2.5;

    protected static function cmsPagePermission(): string
    {
        return 'manage_modules';
    }

    public function mount(): void
    {
        $config = module_config('server_status');
        $parsed = ServerStatusServers::fromConfig($config);

        $this->servers = array_map(function (array $row): array {
            return [
                'label' => $row['label'],
                'host' => $row['host'],
                'port' => $row['port'],
                'query_port' => $row['query_port'],
                'type' => $row['type'],
                'game' => $row['game'],
                'mod_type' => $row['mod_type'] ?? 'vanilla',
                'banner' => (string) ($row['banner'] ?? ''),
            ];
        }, $parsed);

        if ($this->servers === []) {
            $this->servers = [$this->emptyServer()];
        }

        $this->page_title = (string) ($config['page_title'] ?? 'Server-Status');
        $this->page_intro = (string) ($config['page_intro'] ?? '');
        $this->widget_title = (string) ($config['widget_title'] ?? 'Server-Status');
        $this->cache_seconds = (int) ($config['cache_seconds'] ?? 30);
        $this->timeout = (float) ($config['timeout'] ?? 2.5);
    }

    public function addServer(): void
    {
        $this->servers[] = $this->emptyServer();
    }

    public function removeServer(int $index): void
    {
        if (! isset($this->servers[$index])) {
            return;
        }
        unset($this->servers[$index], $this->bannerUploads[$index]);
        $this->servers = array_values($this->servers);
        $this->bannerUploads = array_values($this->bannerUploads);
        if ($this->servers === []) {
            $this->servers = [$this->emptyServer()];
        }
    }

    public function removeBanner(int $index): void
    {
        if (! isset($this->servers[$index])) {
            return;
        }
        $this->servers[$index]['banner'] = '';
        unset($this->bannerUploads[$index]);
    }

    public function updatedBannerUploads($value, string $key): void
    {
        $index = (int) $key;
        $this->validate([
            "bannerUploads.{$index}" => 'nullable|image|max:5120',
        ]);
    }

    public function save(): void
    {
        $this->validate([
            'bannerUploads.*' => 'nullable|image|max:5120',
        ]);

        $cleaned = [];
        foreach ($this->servers as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $host = trim((string) ($row['host'] ?? ''));
            if ($host === '') {
                continue;
            }
            $port = (int) ($row['port'] ?? 25565);
            $queryPort = (int) ($row['query_port'] ?? $port);
            if ($queryPort < 1) {
                $queryPort = $port;
            }

            $banner = trim((string) ($row['banner'] ?? ''));
            $upload = $this->bannerUploads[$index] ?? null;
            if ($upload instanceof TemporaryUploadedFile) {
                $path = $upload->store('server-banners', 'public');
                if (is_string($path) && $path !== '') {
                    $banner = $path;
                }
            }

            $cleaned[] = [
                'label' => trim((string) ($row['label'] ?? '')) ?: $host,
                'host' => $host,
                'port' => max(1, min(65535, $port ?: 25565)),
                'query_port' => max(1, min(65535, $queryPort)),
                'type' => strtolower(trim((string) ($row['type'] ?? 'auto'))) ?: 'auto',
                'game' => trim((string) ($row['game'] ?? '')),
                'mod_type' => in_array(($mt = strtolower(trim((string) ($row['mod_type'] ?? 'vanilla')))), ['vanilla', 'modded'], true) ? $mt : 'vanilla',
                'banner' => $banner,
            ];
        }

        set_module_config('server_status', [
            'servers' => $cleaned,
            'page_title' => trim($this->page_title) ?: 'Server-Status',
            'page_intro' => trim($this->page_intro),
            'widget_title' => trim($this->widget_title) ?: 'Server-Status',
            'cache_seconds' => max(0, (int) $this->cache_seconds),
            'timeout' => max(0.5, (float) $this->timeout),
        ]);

        $this->bannerUploads = [];
        $this->servers = array_map(static function (array $row): array {
            return [
                'label' => $row['label'],
                'host' => $row['host'],
                'port' => $row['port'],
                'query_port' => $row['query_port'],
                'type' => $row['type'],
                'game' => $row['game'],
                'mod_type' => $row['mod_type'] ?? 'vanilla',
                'banner' => (string) ($row['banner'] ?? ''),
            ];
        }, $cleaned);
        if ($this->servers === []) {
            $this->servers = [$this->emptyServer()];
        }

        Cache::flush();

        Notification::make()
            ->title(__('servers.acp_saved'))
            ->success()
            ->send();
    }

    public function bannerPreviewUrl(int $index): ?string
    {
        $upload = $this->bannerUploads[$index] ?? null;
        if ($upload instanceof TemporaryUploadedFile) {
            try {
                return $upload->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        $banner = trim((string) ($this->servers[$index]['banner'] ?? ''));
        if ($banner === '') {
            return null;
        }

        return str_starts_with($banner, 'http://') || str_starts_with($banner, 'https://')
            ? $banner
            : storage_asset($banner);
    }

    /**
     * @return array{label: string, host: string, port: int, query_port: int, type: string, game: string, banner: string}
     */
    protected function emptyServer(): array
    {
        return [
            'label' => '',
            'host' => '',
            'port' => 25565,
            'query_port' => 25565,
            'type' => 'auto',
            'game' => '',
            'mod_type' => 'vanilla',
            'banner' => '',
        ];
    }
}

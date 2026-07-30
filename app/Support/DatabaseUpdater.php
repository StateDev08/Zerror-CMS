<?php

namespace App\Support;

use Database\Seeders\HomeWidgetSeeder;
use Database\Seeders\LegalPagesSeeder;
use Database\Seeders\MenuItemSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Artisan;
use Throwable;

/**
 * Sicheres CMS-Update ohne Neuinstallation: Migrationen + optionale Seeders + Cache.
 */
class DatabaseUpdater
{
    /**
     * @param  array{
     *     sync_permissions?: bool,
     *     sync_menus?: bool,
     *     sync_legal_pages?: bool,
     *     sync_home_widgets?: bool,
     *     clear_caches?: bool
     * }  $options
     * @return array{ok: bool, steps: list<array{label: string, ok: bool, detail: string}>}
     */
    public function run(array $options = []): array
    {
        $steps = [];

        $steps[] = $this->step(__('update_db.step_migrate'), function () {
            $code = Artisan::call('migrate', ['--force' => true]);
            $out = trim(Artisan::output());

            return [$code === 0, $out !== '' ? $out : __('update_db.migrate_done')];
        });

        if (! empty($options['sync_permissions'])) {
            $steps[] = $this->step(__('update_db.step_permissions'), function () {
                Artisan::call('db:seed', ['--force' => true, '--class' => RolePermissionSeeder::class]);

                return [true, trim(Artisan::output()) ?: __('update_db.seed_ok')];
            });
            $steps[] = $this->step('Clan-Ränge synchronisieren', function () {
                Artisan::call('db:seed', ['--force' => true, '--class' => RankSeeder::class]);

                return [true, trim(Artisan::output()) ?: __('update_db.seed_ok')];
            });
        }

        if (! empty($options['sync_menus'])) {
            $steps[] = $this->step(__('update_db.step_menus'), function () {
                Artisan::call('db:seed', ['--force' => true, '--class' => MenuItemSeeder::class]);

                return [true, trim(Artisan::output()) ?: __('update_db.seed_ok')];
            });
        }

        if (! empty($options['sync_legal_pages'])) {
            $steps[] = $this->step(__('update_db.step_legal'), function () {
                Artisan::call('db:seed', ['--force' => true, '--class' => LegalPagesSeeder::class]);

                return [true, trim(Artisan::output()) ?: __('update_db.seed_ok')];
            });
        }

        if (! empty($options['sync_home_widgets'])) {
            $steps[] = $this->step(__('update_db.step_widgets'), function () {
                if (! class_exists(HomeWidgetSeeder::class)) {
                    return [true, __('update_db.seed_skipped')];
                }
                Artisan::call('db:seed', ['--force' => true, '--class' => HomeWidgetSeeder::class]);

                return [true, trim(Artisan::output()) ?: __('update_db.seed_ok')];
            });
        }

        if ($options['clear_caches'] ?? true) {
            $steps[] = $this->step(__('update_db.step_cache'), function () {
                Artisan::call('optimize:clear');

                return [true, trim(Artisan::output()) ?: __('update_db.cache_cleared')];
            });
        }

        $ok = true;
        foreach ($steps as $step) {
            if (! $step['ok']) {
                $ok = false;
                break;
            }
        }

        return ['ok' => $ok, 'steps' => $steps];
    }

    /**
     * @param  callable(): array{0: bool, 1: string}  $callback
     * @return array{label: string, ok: bool, detail: string}
     */
    protected function step(string $label, callable $callback): array
    {
        try {
            [$ok, $detail] = $callback();

            return [
                'label' => $label,
                'ok' => (bool) $ok,
                'detail' => $this->truncate((string) $detail),
            ];
        } catch (Throwable $e) {
            report($e);

            return [
                'label' => $label,
                'ok' => false,
                'detail' => $e->getMessage(),
            ];
        }
    }

    protected function truncate(string $text, int $max = 2000): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max).'…';
    }
}

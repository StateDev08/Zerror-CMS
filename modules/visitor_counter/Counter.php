<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! class_exists('ZerroVisitorCounter', false)) {
    class ZerroVisitorCounter
    {
        public const SETTING_KEY = 'visitor_counter_total';

        public static function current(): int
        {
            if (! self::tableReady()) {
                return self::startValue();
            }

            $raw = Setting::query()->where('key', self::SETTING_KEY)->value('value');
            if ($raw === null || $raw === '') {
                return self::startValue();
            }

            return max(0, (int) $raw);
        }

        public static function hitAndCount(): int
        {
            if (! self::shouldTrack()) {
                return self::current();
            }

            if (! self::tableReady()) {
                return self::startValue();
            }

            $unique = filter_var(
                module_config('visitor_counter')['unique_per_session'] ?? true,
                FILTER_VALIDATE_BOOLEAN
            );

            if ($unique && session()->get('visitor_counter_hit')) {
                return self::current();
            }

            try {
                $total = DB::transaction(function () {
                    $row = Setting::query()
                        ->where('key', self::SETTING_KEY)
                        ->lockForUpdate()
                        ->first();

                    if (! $row) {
                        $next = self::startValue() + 1;
                        Setting::query()->create([
                            'key' => self::SETTING_KEY,
                            'value' => (string) $next,
                        ]);

                        return $next;
                    }

                    $next = max(0, (int) $row->value) + 1;
                    $row->value = (string) $next;
                    $row->save();

                    return $next;
                });

                Cache::forget('setting.'.self::SETTING_KEY);

                if ($unique) {
                    session()->put('visitor_counter_hit', true);
                }

                return $total;
            } catch (\Throwable $e) {
                report($e);

                return self::current();
            }
        }

        protected static function startValue(): int
        {
            $cfg = module_config('visitor_counter');

            return max(0, (int) ($cfg['start_value'] ?? 0));
        }

        protected static function shouldTrack(): bool
        {
            if (app()->runningInConsole()) {
                return false;
            }

            $request = request();
            if (! $request) {
                return false;
            }

            if ($request->is('admin', 'admin/*', 'livewire/*', 'filament/*')) {
                return false;
            }

            $ua = strtolower((string) $request->userAgent());
            if ($ua === '') {
                return false;
            }

            foreach (['bot', 'spider', 'crawl', 'slurp', 'facebookexternalhit', 'preview'] as $needle) {
                if (str_contains($ua, $needle)) {
                    return false;
                }
            }

            return true;
        }

        protected static function tableReady(): bool
        {
            try {
                return Schema::hasTable('settings');
            } catch (\Throwable $e) {
                return false;
            }
        }
    }
}

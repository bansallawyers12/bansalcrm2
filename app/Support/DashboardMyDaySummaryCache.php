<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardMyDaySummaryCache
{
    public const TTL_SECONDS = 300;

    public static function cacheKey(int $staffId, ?string $dayYmd = null): string
    {
        $tz = (string) config('app.timezone', 'Australia/Melbourne');
        $day = $dayYmd ?? now()->timezone($tz)->format('Y-m-d');

        return "dashboard:my_day_summary:v1:{$staffId}:{$day}";
    }

    /**
     * @param  callable(): array<string, mixed>  $resolver
     * @return array<string, mixed>
     */
    public static function remember(int $staffId, callable $resolver): array
    {
        return Cache::remember(
            self::cacheKey($staffId),
            self::TTL_SECONDS,
            fn () => self::normalizeForStorage($resolver())
        );
    }

    public static function forget(int $staffId): void
    {
        Cache::forget(self::cacheKey($staffId));
    }

    /**
     * Strip Carbon instances so Laravel 13 cache stores can serialize the payload.
     *
     * @param  array<string, mixed>  $summary
     * @return array<string, mixed>
     */
    public static function normalizeForStorage(array $summary): array
    {
        if (isset($summary['day']) && $summary['day'] instanceof Carbon) {
            $summary['day'] = $summary['day']->toIso8601String();
        }

        if (isset($summary['range']['start']) && $summary['range']['start'] instanceof Carbon) {
            $summary['range']['start'] = $summary['range']['start']->toIso8601String();
        }

        if (isset($summary['range']['end']) && $summary['range']['end'] instanceof Carbon) {
            $summary['range']['end'] = $summary['range']['end']->toIso8601String();
        }

        return $summary;
    }
}

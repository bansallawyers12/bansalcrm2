<?php

namespace Tests\Unit\Support;

use App\Support\DashboardMyDaySummaryCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardMyDaySummaryCacheTest extends TestCase
{
    public function test_cache_key_includes_staff_id_and_day(): void
    {
        config(['app.timezone' => 'Australia/Melbourne']);

        $this->assertSame(
            'dashboard:my_day_summary:v1:42:2026-09-26',
            DashboardMyDaySummaryCache::cacheKey(42, '2026-09-26')
        );
    }

    public function test_normalize_for_storage_converts_carbon_fields_to_strings(): void
    {
        $tz = 'Australia/Melbourne';
        $day = Carbon::parse('2026-09-26', $tz);
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        $normalized = DashboardMyDaySummaryCache::normalizeForStorage([
            'day' => $day,
            'day_label' => 'Friday, 26 September 2026',
            'range' => ['start' => $start, 'end' => $end],
            'caseload' => ['active_clients_count' => 3],
        ]);

        $this->assertIsString($normalized['day']);
        $this->assertIsString($normalized['range']['start']);
        $this->assertIsString($normalized['range']['end']);
        $this->assertSame(3, $normalized['caseload']['active_clients_count']);
    }

    public function test_remember_caches_summary_and_forget_clears_it(): void
    {
        Cache::flush();

        $calls = 0;
        $resolver = function () use (&$calls) {
            $calls++;

            return DashboardMyDaySummaryCache::normalizeForStorage([
                'day_label' => 'cached',
                'caseload' => ['active_clients_count' => $calls],
            ]);
        };

        $first = DashboardMyDaySummaryCache::remember(7, $resolver);
        $second = DashboardMyDaySummaryCache::remember(7, $resolver);

        $this->assertSame(1, $calls);
        $this->assertSame($first, $second);

        DashboardMyDaySummaryCache::forget(7);

        $third = DashboardMyDaySummaryCache::remember(7, $resolver);
        $this->assertSame(2, $calls);
        $this->assertSame(2, $third['caseload']['active_clients_count']);
    }
}

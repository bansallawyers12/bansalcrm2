<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DashboardMyDayDiaryTest extends TestCase
{
    public function test_diary_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('dashboard.my-day.diary'));
        $this->assertTrue(Route::has('dashboard.my-day.file-time.log'));
        $this->assertTrue(Route::has('dashboard.my-day.copy-summary'));
        $this->assertTrue(Route::has('dashboard.my-day.copy-summary.save'));
        $this->assertTrue(Route::has('dashboard.my-day.record-search'));
    }

    public function test_guest_cannot_load_diary(): void
    {
        $this->getJson(route('dashboard.my-day.diary'))->assertUnauthorized();
        $this->getJson(route('dashboard.my-day.copy-summary'))->assertUnauthorized();
        $this->postJson(route('dashboard.my-day.copy-summary.save'))->assertUnauthorized();
    }

    public function test_admin_console_staff_workload_show_does_not_include_diary_partial(): void
    {
        $path = resource_path('views/AdminConsole/staff_workload/show.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringNotContainsString('my-day-diary', $contents);
        $this->assertStringNotContainsString('dashboard-diary.js', $contents);
        $this->assertStringContainsString('my-day-panel', $contents);
        $this->assertStringContainsString('End-of-day summary', $contents);
    }

    public function test_admin_console_index_has_summary_saved_column_not_auto_minutes(): void
    {
        $path = resource_path('views/AdminConsole/staff_workload/index.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('Summary saved?', $contents);
        $this->assertStringNotContainsString('auto minutes', strtolower($contents));
        $this->assertStringNotContainsString('my-day-diary', $contents);
    }

    public function test_personal_dashboard_includes_diary_partial(): void
    {
        $path = resource_path('views/Admin/dashboard.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('my-day-diary', $contents);
        $this->assertStringContainsString('my-day-panel', $contents);
        $this->assertMatchesRegularExpression(
            '/@auth\([\'"]admin[\'"]\).*my-day-diary.*@endauth.*@if\(!empty\(\$myDaySummary\)\).*my-day-panel/s',
            $contents
        );
    }

    public function test_log_minutes_cancel_bypasses_html_validation(): void
    {
        $path = resource_path('views/Admin/partials/my-day-diary.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertMatchesRegularExpression(
            '/value=["\']cancel["\'][^>]*\bformnovalidate\b|\bformnovalidate\b[^>]*value=["\']cancel["\']/',
            $contents
        );
        $this->assertStringContainsString('value="save"', $contents);
        $this->assertDoesNotMatchRegularExpression(
            '/value=["\']save["\'][^>]*\bformnovalidate\b|\bformnovalidate\b[^>]*value=["\']save["\']/',
            $contents
        );
    }

    public function test_dashboard_diary_js_renders_ref_links_when_url_present(): void
    {
        $path = public_path('js/my-day/dashboard-diary.js');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('function formatRef', $contents);
        $this->assertStringContainsString('my-day-diary-ref', $contents);
        $this->assertStringContainsString('item.url', $contents);
        $this->assertStringContainsString('my-day-crm-show-more', $contents);
        $this->assertStringContainsString('item.body', $contents);
    }

    public function test_client_and_partner_detail_entries_include_deep_link_highlight(): void
    {
        $clientEntry = file_get_contents(resource_path('js/pages/admin/client-detail-entry.js'));
        $partnerEntry = file_get_contents(resource_path('js/pages/admin/partner-detail-entry.js'));
        $this->assertStringContainsString('deep-link-highlight.js', $clientEntry);
        $this->assertStringContainsString('deep-link-highlight.js', $partnerEntry);
        $this->assertFileExists(resource_path('js/pages/admin/client-detail/deep-link-highlight.js'));
        $highlight = file_get_contents(resource_path('js/pages/admin/client-detail/deep-link-highlight.js'));
        $this->assertStringContainsString('initActivityDeepLinkHighlight', $highlight);
        $this->assertStringContainsString('deep-link-highlight', $highlight);
        $this->assertStringContainsString('app_stage_log_', $highlight);
        $this->assertStringContainsString("block: 'start'", $highlight);
    }
}

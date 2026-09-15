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
        $this->assertTrue(Route::has('dashboard.my-day.record-search'));
    }

    public function test_guest_cannot_load_diary(): void
    {
        $this->getJson(route('dashboard.my-day.diary'))->assertUnauthorized();
        $this->getJson(route('dashboard.my-day.copy-summary'))->assertUnauthorized();
    }

    public function test_admin_console_staff_workload_show_does_not_include_diary_partial(): void
    {
        $path = resource_path('views/AdminConsole/staff_workload/show.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringNotContainsString('my-day-diary', $contents);
        $this->assertStringContainsString('my-day-panel', $contents);
    }

    public function test_personal_dashboard_includes_diary_partial(): void
    {
        $path = resource_path('views/Admin/dashboard.blade.php');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('my-day-diary', $contents);
        $this->assertStringContainsString('my-day-panel', $contents);
    }
}

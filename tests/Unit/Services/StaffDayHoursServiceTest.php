<?php

namespace Tests\Unit\Services;

use App\Models\Staff;
use App\Services\DashboardService;
use App\Services\StaffDayHoursService;
use App\Services\StaffWorkloadService;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffDayHoursServiceTest extends TestCase
{
    #[Test]
    public function for_staff_formats_hours_and_minutes_not_raw_seconds(): void
    {
        config(['app.timezone' => 'Australia/Melbourne']);

        $staff = new Staff([
            'first_name' => 'Test',
            'last_name' => 'Staff',
        ]);
        $staff->id = 7;
        Auth::guard('admin')->setUser($staff);

        $dashboard = $this->createStub(DashboardService::class);
        $dashboard->method('getLoginStatistics')->willReturn([
            // Simulate Carbon 3 signed float leak if not normalized upstream.
            'current_session_duration' => -1208.44666,
            'current_session_duration_formatted' => '-1208.44666 seconds',
        ]);

        $service = new StaffDayHoursService(new StaffWorkloadService, $dashboard);
        $payload = $service->forStaff(7);

        $this->assertSame('20m', $payload['label']);
        $this->assertSame(20, $payload['minutes']);
        $this->assertSame(1208, $payload['seconds']);
        $this->assertStringNotContainsString('second', strtolower($payload['label']));
    }

    #[Test]
    public function format_hours_and_minutes_includes_hours_when_needed(): void
    {
        $service = new StaffDayHoursService(
            new StaffWorkloadService,
            $this->createStub(DashboardService::class),
        );

        $method = new \ReflectionMethod(StaffDayHoursService::class, 'formatHoursAndMinutes');
        $method->setAccessible(true);

        $this->assertSame('0m', $method->invoke($service, 0));
        $this->assertSame('5m', $method->invoke($service, 320));
        $this->assertSame('1h', $method->invoke($service, 3600));
        $this->assertSame('2h 5m', $method->invoke($service, 7500));
    }
}

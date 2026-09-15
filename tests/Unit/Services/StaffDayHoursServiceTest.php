<?php

namespace Tests\Unit\Services;

use App\Models\Staff;
use App\Services\DashboardService;
use App\Services\StaffDayHoursService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    #[Test]
    public function for_other_staff_uses_login_logs_clamped_to_the_melbourne_day(): void
    {
        config(['app.timezone' => 'Australia/Melbourne']);
        Carbon::setTestNow(Carbon::parse('2026-09-15 18:00:00', 'Australia/Melbourne'));

        Schema::dropIfExists('staff_login_logs');
        Schema::create('staff_login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('message')->nullable();
            $table->timestamps();
        });

        Auth::guard('admin')->forgetUser();

        DB::table('staff_login_logs')->insert([
            'user_id' => 9,
            'message' => 'Logged in successfully',
            'created_at' => '2026-09-15 16:00:00',
            'updated_at' => '2026-09-15 16:00:00',
        ]);

        $service = new StaffDayHoursService(
            new StaffWorkloadService,
            $this->createStub(DashboardService::class),
        );
        $payload = $service->forStaff(9);

        $this->assertSame('2h', $payload['label']);
        $this->assertSame('login_log', $payload['source']);

        Carbon::setTestNow();
        Schema::dropIfExists('staff_login_logs');
    }
}

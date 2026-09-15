<?php

namespace Tests\Unit\Services;

use App\Models\StaffDaySummary;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffDaySummaryService;
use App\Services\StaffFileSessionService;
use App\Services\StaffFileTimeService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffDaySummaryServiceTest extends TestCase
{
    private StaffDaySummaryService $service;

    private function makeService(?StaffFileTimeService $fileTime = null): StaffDaySummaryService
    {
        return new StaffDaySummaryService(
            new StaffWorkloadService,
            $fileTime ?? $this->createStub(StaffFileTimeService::class),
            $this->createStub(StaffDayCrmEventsService::class),
            $this->createStub(StaffDayHoursService::class),
            $this->createStub(StaffFileSessionService::class),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = $this->makeService();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    #[Test]
    public function upsert_replaces_same_staff_and_date(): void
    {
        $day = Carbon::parse('2026-09-15 18:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($day);

        $first = $this->service->upsert(1, ['text' => 'first paste'], StaffDaySummary::SOURCE_COPY, $day);
        $second = $this->service->upsert(1, ['text' => 'second paste'], StaffDaySummary::SOURCE_COPY, $day);

        $this->assertSame($first->id, $second->id);
        $this->assertSame('second paste', $second->body);
        $this->assertSame(1, StaffDaySummary::query()->count());
    }

    #[Test]
    public function find_returns_saved_row_for_melbourne_date(): void
    {
        $day = Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne');
        $this->service->upsert(1, ['text' => 'Ajay — Tuesday'], StaffDaySummary::SOURCE_COPY, $day);

        $found = $this->service->find(1, Carbon::parse('2026-09-15 23:50:00', 'Australia/Melbourne'));

        $this->assertNotNull($found);
        $this->assertSame('Ajay — Tuesday', $found->body);
        $this->assertNull($this->service->find(1, Carbon::parse('2026-09-14 12:00:00', 'Australia/Melbourne')));
    }

    #[Test]
    public function admin_review_falls_back_to_previous_day_after_midnight(): void
    {
        $this->service->upsert(
            1,
            ['text' => 'Monday wrap'],
            StaffDaySummary::SOURCE_SCHEDULE,
            Carbon::parse('2026-09-14 23:55:00', 'Australia/Melbourne'),
        );

        Carbon::setTestNow(Carbon::parse('2026-09-15 09:10:00', 'Australia/Melbourne'));

        $found = $this->service->findForAdminReview(1);
        $payload = $this->service->payload($found);

        $this->assertNotNull($found);
        $this->assertSame('Monday wrap', $found->body);
        $this->assertSame('2026-09-14', $payload['summary_date']);
        $this->assertSame('nightly snapshot', $payload['source_label']);
        $this->assertTrue($this->service->savedStaffIdsForAdminIndex([1])->has(1));
        $this->assertFalse($this->service->savedStaffIdsForDay([1])->has(1));
    }

    #[Test]
    public function snapshot_active_staff_skips_inactive(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 23:55:00', 'Australia/Melbourne'));
        DB::table('staff')->insert([
            ['id' => 1, 'first_name' => 'On', 'last_name' => 'Duty', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'first_name' => 'Off', 'last_name' => 'Duty', 'status' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $fileTime = $this->createMock(StaffFileTimeService::class);
        $fileTime->expects($this->once())
            ->method('copySummary')
            ->willReturn(['text' => 'On Duty day']);

        $service = $this->makeService($fileTime);
        $count = $service->snapshotActiveStaff(StaffDaySummary::SOURCE_SCHEDULE);

        $this->assertSame(1, $count);
        $this->assertSame('On Duty day', StaffDaySummary::query()->where('staff_id', 1)->value('body'));
        $this->assertSame(0, StaffDaySummary::query()->where('staff_id', 2)->count());
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('staff_day_summaries');
        Schema::dropIfExists('staff');

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_day_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->date('summary_date');
            $table->text('body');
            $table->string('source', 16)->default('copy');
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'summary_date']);
        });
    }
}

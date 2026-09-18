<?php

namespace Tests\Unit\Services;

use App\Models\ActivitiesLog;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class StaffWorkloadServiceTest extends TestCase
{
    private function service(): StaffWorkloadService
    {
        return new StaffWorkloadService;
    }

    public function test_day_bounds_use_app_timezone_start_and_end(): void
    {
        config(['app.timezone' => 'Australia/Melbourne']);

        $day = Carbon::parse('2026-09-01', 'Australia/Melbourne');
        [$start, $end] = $this->service()->dayBounds($day);

        $this->assertSame('2026-09-01 00:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-01 23:59:59', $end->format('Y-m-d H:i:s'));
        $this->assertSame('Australia/Melbourne', $start->timezone->getName());
    }

    public function test_note_audit_subjects_are_excluded_from_throughput_activity_credit(): void
    {
        $service = $this->service();

        $this->assertTrue($service->isNoteAuditSubject('added a note'));
        $this->assertTrue($service->isNoteAuditSubject('Updated a note'));
        $this->assertFalse($service->isNoteAuditSubject('uploaded document'));
    }

    public function test_inactivity_band_quiet_and_inactive_thresholds(): void
    {
        $service = $this->service();
        $method = new ReflectionMethod(StaffWorkloadService::class, 'inactivityBand');
        $method->setAccessible(true);

        config(['app.timezone' => 'Australia/Melbourne']);
        $today = Carbon::parse('2026-09-01', 'Australia/Melbourne')->startOfDay();

        $this->assertSame('inactive', $method->invoke($service, null, $today));

        $tenDaysAgo = $today->copy()->subDays(10);
        $this->assertSame('quiet', $method->invoke($service, $tenDaysAgo, $today));

        $fifteenDaysAgo = $today->copy()->subDays(15);
        $this->assertSame('inactive', $method->invoke($service, $fifteenDaysAgo, $today));

        $threeDaysAgo = $today->copy()->subDays(3);
        $this->assertNull($method->invoke($service, $threeDaysAgo, $today));
    }

    public function test_encode_record_id_is_reversible(): void
    {
        $encoded = $this->service()->encodeRecordId(42);
        $decoded = convert_uudecode(base64_decode($encoded));

        $this->assertSame('42', $decoded);
    }

    public function test_contact_titles_are_call_and_in_person_only(): void
    {
        $this->assertSame(['Call', 'In-Person'], StaffWorkloadService::CONTACT_TITLES);
    }

    public function test_diary_deep_link_fragment_from_feed_note_and_activities_log(): void
    {
        $service = $this->service();

        $this->assertSame('activity_99', $service->diaryDeepLinkFragment(['key' => 'feed:99']));
        $this->assertSame('note_id_12', $service->diaryDeepLinkFragment(['key' => 'note:12']));
        $this->assertSame(
            'activity_55',
            $service->diaryDeepLinkFragment(['key' => 'note:12', 'activities_log_id' => 55])
        );
        $this->assertSame('app_stage_log_44', $service->diaryDeepLinkFragment(['key' => 'stage:44']));
        $this->assertSame('activity_7', $service->diaryDeepLinkFragment(['activities_log_id' => 7]));
        $this->assertSame(
            'activity_8',
            $service->diaryDeepLinkFragment(['key' => 'email:3', 'activities_log_id' => 8])
        );
        $this->assertNull($service->diaryDeepLinkFragment(['key' => 'email:3']));
        $this->assertNull($service->diaryDeepLinkFragment([]));
    }

    public function test_attach_diary_record_links_adds_client_url_and_note_hash(): void
    {
        $service = $this->service();
        $encoded = $service->encodeRecordId(10);

        Schema::dropIfExists('admins');
        Schema::create('admins', function ($table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->timestamps();
        });
        DB::table('admins')->insert([
            'id' => 10,
            'type' => 'client',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $linked = $service->attachDiaryRecordLinks([
            [
                'key' => 'note:5',
                'ref' => 'STU10',
                'record_type' => 'student',
                'record_id' => 10,
            ],
            [
                'key' => 'note:6',
                'ref' => 'STU10',
                'record_type' => 'student',
                'record_id' => 10,
                'activities_log_id' => 88,
            ],
            [
                'key' => 'stage:9',
                'ref' => 'STU10',
                'record_type' => 'student',
                'record_id' => 10,
                'application_id' => 3,
            ],
            [
                'key' => 'email:2',
                'ref' => 'STU10',
                'record_type' => 'student',
                'record_id' => 10,
                'activities_log_id' => 77,
            ],
            [
                'ref' => 'No record',
            ],
        ]);

        $this->assertSame(
            route('clients.detail', ['id' => $encoded]).'#note_id_5',
            $linked[0]['url']
        );
        $this->assertSame('note_id_5', $linked[0]['deep_link']);
        $this->assertSame(
            route('clients.detail', ['id' => $encoded]).'#activity_88',
            $linked[1]['url']
        );
        $this->assertSame('activity_88', $linked[1]['deep_link']);
        $this->assertSame(
            route('clients.detail.application', ['id' => $encoded, 'applicationId' => 3]).'#app_stage_log_9',
            $linked[2]['url']
        );
        $this->assertSame('app_stage_log_9', $linked[2]['deep_link']);
        $this->assertSame(
            route('clients.detail', ['id' => $encoded]).'#activity_77',
            $linked[3]['url']
        );
        $this->assertSame('activity_77', $linked[3]['deep_link']);
        $this->assertArrayNotHasKey('url', $linked[4]);
    }

    public function test_student_activity_scope_keeps_null_and_non_partner_task_groups(): void
    {
        $query = ActivitiesLog::query()->forStudentRecords();
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('task_group', $sql);
        $this->assertContains(ActivitiesLog::TASK_GROUP_PARTNER, $query->getBindings());
    }
}

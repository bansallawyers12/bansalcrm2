<?php

namespace Tests\Unit\Services;

use App\Models\StaffFileSession;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffFileSessionService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffFileSessionServiceTest extends TestCase
{
    private StaffFileSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffFileSessionService(
            new StaffWorkloadService,
            new StaffDayCrmEventsService(new StaffWorkloadService),
        );
    }

    #[Test]
    public function heartbeat_creates_one_session_per_staff_record_application_and_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        $first = $this->service->heartbeat(1, 'student', 10, 5, 30);
        Carbon::setTestNow(Carbon::parse('2026-09-15 09:01:00', 'Australia/Melbourne'));
        $second = $this->service->heartbeat(1, 'student', 10, 5, 90);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, DB::table('staff_file_sessions')->count());
        $this->assertSame(90, (int) $second->focused_seconds);
    }

    #[Test]
    public function student_and_partner_with_same_numeric_id_do_not_collide(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertStudent(5910);
        $this->insertPartner(5910);

        $student = $this->service->heartbeat(1, 'student', 5910, null, 30);
        $partner = $this->service->heartbeat(1, 'partner', 5910, null, 45);

        $this->assertNotSame($student->id, $partner->id);
        $this->assertSame(2, DB::table('staff_file_sessions')->count());
    }

    #[Test]
    public function stale_close_uses_last_heartbeat_not_now(): void
    {
        $beat = Carbon::parse('2026-09-15 10:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($beat);
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        $session = $this->service->heartbeat(1, 'student', 10, 5, 600);
        $this->service->promoteIfWritten($session->fresh());

        Carbon::setTestNow($beat->copy()->addMinutes(10));
        $this->service->closeStale(now());

        $closed = StaffFileSession::query()->find($session->id);
        $this->assertSame(StaffFileSession::STATUS_CLOSED, $closed->status);
        $this->assertTrue($closed->ended_at->equalTo($beat));
    }

    #[Test]
    public function idle_cut_reduces_focused_seconds(): void
    {
        $start = Carbon::parse('2026-09-15 11:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertStudent(10);

        $session = $this->service->heartbeat(1, 'student', 10, null, 0);
        $session->focused_seconds = 900;
        $session->last_heartbeat_at = $start->copy()->addMinutes(15);
        $session->save();

        $idleStart = $start->copy()->addMinutes(10);
        $updated = $this->service->idleCut(1, $session->fresh(), $idleStart);

        $this->assertSame(600, (int) $updated->focused_seconds);
        $this->assertSame(300, (int) $updated->idle_cut_seconds);
    }

    #[Test]
    public function promotion_from_note_inside_window_makes_recorded(): void
    {
        $start = Carbon::parse('2026-09-15 12:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        $this->service->heartbeat(1, 'student', 10, 5, 60);

        DB::table('notes')->insert([
            'id' => 1,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'title' => 'Call',
            'created_at' => $start->copy()->addMinute(),
            'updated_at' => $start->copy()->addMinute(),
        ]);

        Carbon::setTestNow($start->copy()->addMinutes(2));
        $promoted = $this->service->heartbeat(1, 'student', 10, 5, 120);

        $this->assertSame(StaffFileSession::STATUS_RECORDED, $promoted->status);
        $this->assertFalse((bool) $promoted->is_reviewed_only);
    }

    #[Test]
    public function two_minutes_without_write_becomes_reviewed_only_recorded(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:30:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        $session = $this->service->heartbeat(1, 'student', 10, 5, 130);
        $promoted = $this->service->promoteIfWritten($session);

        $this->assertSame(StaffFileSession::STATUS_RECORDED, $promoted->status);
        $this->assertTrue((bool) $promoted->is_reviewed_only);
    }

    #[Test]
    public function close_recorded_writes_file_time_feed_row_without_use_for(): void
    {
        $start = Carbon::parse('2026-09-15 13:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        $session = $this->service->heartbeat(1, 'student', 10, 5, 300);
        $this->service->promoteIfWritten($session->fresh());

        Carbon::setTestNow($start->copy()->addMinutes(5));
        $this->service->closeStale(now());

        $closed = StaffFileSession::query()->find($session->id);
        $this->assertNotNull($closed->activities_log_id);
        $log = DB::table('activities_logs')->where('id', $closed->activities_log_id)->first();
        $this->assertSame('file_time', $log->activity_type);
        $this->assertSame(0, (int) $log->task_status);
        $this->assertSame(0, (int) $log->pin);
        $this->assertNull($log->use_for);
        $this->assertNull($log->task_group);
        $this->assertStringContainsString('logged 5m', (string) $log->subject);
    }

    #[Test]
    public function partner_close_posts_with_partner_task_group(): void
    {
        $start = Carbon::parse('2026-09-15 13:20:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertPartner(20);

        $session = $this->service->heartbeat(1, 'partner', 20, null, 300);
        $this->service->promoteIfWritten($session->fresh());

        Carbon::setTestNow($start->copy()->addMinutes(5));
        $this->service->closeStale(now());

        $closed = StaffFileSession::query()->find($session->id);
        $log = DB::table('activities_logs')->where('id', $closed->activities_log_id)->first();
        $this->assertSame('partner', $log->task_group);
        $this->assertSame(20, (int) $log->client_id);
        $this->assertNull($log->use_for);
    }

    #[Test]
    public function close_accessed_writes_no_feed_row(): void
    {
        $start = Carbon::parse('2026-09-15 13:10:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertStudent(10);

        $session = $this->service->heartbeat(1, 'student', 10, null, 30);
        Carbon::setTestNow($start->copy()->addMinutes(5));
        $this->service->closeStale(now());

        $closed = StaffFileSession::query()->find($session->id);
        $this->assertNull($closed->activities_log_id);
        $this->assertSame(0, DB::table('activities_logs')->count());
    }

    #[Test]
    public function split_minutes_sum_to_confirmed_minutes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertStudent(10);
        $this->insertApplication(5, 10);

        DB::table('staff_file_sessions')->insert([
            'id' => 1,
            'staff_id' => 1,
            'record_type' => 'student',
            'record_id' => 10,
            'application_id' => 5,
            'application_key' => 5,
            'session_date' => '2026-09-15',
            'status' => StaffFileSession::STATUS_CLOSED,
            'focused_seconds' => 600,
            'idle_cut_seconds' => 0,
            'confirmed_minutes' => 10,
            'event_count' => 3,
            'is_reviewed_only' => false,
            'started_at' => now()->subHour(),
            'last_heartbeat_at' => now(),
            'ended_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([1, 2, 3] as $i) {
            DB::table('notes')->insert([
                'id' => $i,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'title' => 'Call',
                'created_at' => now()->subMinutes(60 - ($i * 10)),
                'updated_at' => now()->subMinutes(60 - ($i * 10)),
            ]);
        }

        $board = $this->service->sessionsForBoard(1);
        $events = $board['auto'][0]['events'] ?? [];
        $sum = array_sum(array_column($events, 'minutes'));
        $this->assertSame(10, $sum);
        $this->assertSame(4, $events[0]['minutes']);
    }

    #[Test]
    public function file_time_activity_does_not_inflate_throughput_student_count(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertStudent(10);

        $workload = new StaffWorkloadService;
        [$start, $end] = $workload->dayBounds(now());

        $before = $workload->getThroughput(1, $start, $end);

        DB::table('activities_logs')->insert([
            'id' => 1,
            'client_id' => 10,
            'created_by' => 1,
            'subject' => 'logged 5m on Record #10 · reviewed file',
            'description' => 'logged 5m on Record #10 · reviewed file',
            'activity_type' => 'file_time',
            'use_for' => null,
            'task_status' => 0,
            'pin' => 0,
            'task_group' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $after = $workload->getThroughput(1, $start, $end);
        $this->assertSame($before['worked_students_count'], $after['worked_students_count']);
    }

    #[Test]
    public function later_write_clears_reviewed_only_flag(): void
    {
        $start = Carbon::parse('2026-09-15 12:40:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertStudent(10);

        $session = $this->service->heartbeat(1, 'student', 10, null, 130);
        $session = $this->service->promoteIfWritten($session);
        $this->assertTrue((bool) $session->is_reviewed_only);

        DB::table('notes')->insert([
            'id' => 9,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'title' => 'Call',
            'created_at' => $start->copy()->addMinute(),
            'updated_at' => $start->copy()->addMinute(),
        ]);

        Carbon::setTestNow($start->copy()->addMinutes(2));
        $updated = $this->service->heartbeat(1, 'student', 10, null, 200);

        $this->assertSame(StaffFileSession::STATUS_RECORDED, $updated->status);
        $this->assertFalse((bool) $updated->is_reviewed_only);
    }

    private function createSchema(): void
    {
        foreach ([
            'staff_file_sessions',
            'application_activities_logs',
            'activities_logs',
            'notes',
            'emails',
            'documents',
            'sms_logs',
            'applications',
            'partners',
            'admins',
            'staff',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_name')->nullable();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('partner_id')->nullable();
            $table->string('stage')->nullable();
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('is_action')->default(0);
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('type')->nullable();
            $table->string('subject')->nullable();
            $table->string('conversion_type')->nullable();
            $table->string('mail_body_type')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('application_id')->nullable();
            $table->string('type')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->text('message_content')->nullable();
            $table->timestamps();
        });

        Schema::create('application_activities_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('app_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('stage')->nullable();
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('activity_type', 64)->nullable();
            $table->integer('use_for')->nullable();
            $table->integer('task_status')->default(0);
            $table->integer('pin')->default(0);
            $table->string('task_group')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_file_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('record_type', 16);
            $table->unsignedInteger('record_id');
            $table->unsignedInteger('application_id')->nullable();
            $table->unsignedInteger('application_key')->default(0);
            $table->date('session_date');
            $table->string('status', 16)->default('accessed');
            $table->unsignedInteger('focused_seconds')->default(0);
            $table->unsignedInteger('idle_cut_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->unsignedSmallInteger('event_count')->nullable();
            $table->boolean('is_reviewed_only')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();

            $table->unique(
                ['staff_id', 'record_type', 'record_id', 'application_key', 'session_date'],
                'staff_file_sessions_staff_record_day'
            );
        });
    }

    private function insertStaff(int $id): void
    {
        DB::table('staff')->insert([
            'id' => $id,
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertStudent(int $id): void
    {
        DB::table('admins')->insert([
            'id' => $id,
            'type' => 'client',
            'client_id' => 'STU'.$id,
            'first_name' => 'Student',
            'last_name' => (string) $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertPartner(int $id): void
    {
        DB::table('partners')->insert([
            'id' => $id,
            'partner_name' => 'College '.$id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertApplication(int $id, int $clientId): void
    {
        DB::table('applications')->insert([
            'id' => $id,
            'client_id' => $clientId,
            'partner_id' => null,
            'stage' => 'Offer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

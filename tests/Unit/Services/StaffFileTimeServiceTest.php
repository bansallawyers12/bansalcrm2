<?php

namespace Tests\Unit\Services;

use App\Models\StaffFileTimeEntry;
use App\Services\DashboardService;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffFileSessionService;
use App\Services\StaffFileTimeService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffFileTimeServiceTest extends TestCase
{
    private StaffFileTimeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffFileTimeService(new StaffWorkloadService);
    }

    #[Test]
    public function log_completed_with_student_posts_feed_without_use_for(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        $entry = $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_CALL,
            'title' => 'Follow-up call',
            'confirmed_minutes' => 15,
            'record_type' => 'student',
            'record_id' => 10,
        ]);

        $this->assertSame(StaffFileTimeEntry::STATUS_DONE, $entry->status);
        $this->assertNotNull($entry->activities_log_id);
        $log = DB::table('activities_logs')->where('id', $entry->activities_log_id)->first();
        $this->assertSame('file_time', $log->activity_type);
        $this->assertNull($log->use_for);
        $this->assertNull($log->task_group);
    }

    #[Test]
    public function admin_log_does_not_post_feed(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:10:00', 'Australia/Melbourne'));
        $this->seedBasics();

        $entry = $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_INTERNAL,
            'title' => 'Team huddle',
            'confirmed_minutes' => 20,
            'admin' => true,
        ]);

        $this->assertTrue($entry->isAdmin());
        $this->assertNull($entry->activities_log_id);
        $this->assertSame(0, DB::table('activities_logs')->count());
    }

    #[Test]
    public function copy_summary_includes_sections(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:20:00', 'Australia/Melbourne'));
        $this->seedBasics();

        $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_EMAIL,
            'title' => 'Outlook skim',
            'confirmed_minutes' => 10,
            'record_type' => 'student',
            'record_id' => 10,
        ]);

        $summary = $this->service->copySummary(
            1,
            new StaffDayCrmEventsService(new StaffWorkloadService),
            new StaffDayHoursService(new StaffWorkloadService, app(DashboardService::class)),
            new StaffFileSessionService(new StaffWorkloadService, new StaffDayCrmEventsService(new StaffWorkloadService)),
        );

        $this->assertStringContainsString('— Already in CRM —', $summary['text']);
        $this->assertStringContainsString('— Manual logs —', $summary['text']);
        $this->assertStringContainsString('Outlook skim', $summary['text']);
        $this->assertStringContainsString('Hours in CRM:', $summary['text']);
    }

    private function seedBasics(): void
    {
        DB::table('staff')->insert([
            'id' => 1,
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('admins')->insert([
            'id' => 10,
            'type' => 'client',
            'client_id' => 'STU10',
            'first_name' => 'Stu',
            'last_name' => 'Dent',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSchema(): void
    {
        foreach ([
            'staff_file_time_entries',
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
        });

        Schema::create('staff_file_time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('record_type', 16)->nullable();
            $table->unsignedInteger('record_id')->nullable();
            $table->unsignedInteger('application_id')->nullable();
            $table->string('kind', 32);
            $table->string('title');
            $table->string('status', 16)->default('done');
            $table->boolean('is_running')->default(false);
            $table->unsignedInteger('clock_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();
        });
    }
}

<?php

namespace Tests\Unit\Services;

use App\Services\StaffDayCrmEventsService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffDayCrmEventsServiceTest extends TestCase
{
    private StaffDayCrmEventsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffDayCrmEventsService(new StaffWorkloadService);
    }

    #[Test]
    public function for_staff_includes_contact_notes_and_excludes_file_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 10:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('notes')->insert([
            'id' => 1,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'title' => 'Call',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities_logs')->insert([
            'id' => 1,
            'client_id' => 10,
            'created_by' => 1,
            'subject' => 'logged 3m on STU10 · reviewed file',
            'description' => 'logged 3m',
            'activity_type' => 'file_time',
            'task_status' => 0,
            'pin' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $keys = array_column($payload['items'], 'key');

        $this->assertContains('note:1', $keys);
        $this->assertNotContains('feed:1', $keys);
    }

    #[Test]
    public function for_staff_includes_non_contact_note_titles_like_others(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 10:30:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('notes')->insert([
            'id' => 3,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'title' => 'Others',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $item = collect($payload['items'])->firstWhere('key', 'note:3');

        $this->assertNotNull($item);
        $this->assertSame('Note', $item['kind']);
        $this->assertSame('Others', $item['title']);
    }

    #[Test]
    public function for_staff_still_excludes_assigned_action_notes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 10:45:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('notes')->insert([
            [
                'id' => 4,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'is_action' => 1,
                'assigned_to' => null,
                'title' => 'Others',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => 99,
                'title' => 'Others',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $payload = $this->service->forStaff(1);
        $keys = array_column($payload['items'], 'key');

        $this->assertNotContains('note:4', $keys);
        $this->assertNotContains('note:5', $keys);
    }

    #[Test]
    public function for_staff_on_record_attributes_null_application_notes_to_open_application(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 11:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('notes')->insert([
            'id' => 2,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'title' => 'In-Person',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $events = $this->service->forStaffOnRecord(
            1,
            'student',
            10,
            5,
            now()->subHour(),
            now()->addHour(),
        );

        $this->assertCount(1, $events);
        $this->assertSame('note:2', $events->first()['key']);
    }

    #[Test]
    public function for_staff_stage_move_uses_student_client_ref_not_college_application_label(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('partners')->insert([
            'id' => 20,
            'partner_name' => 'Test College',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('applications')->where('id', 5)->update(['partner_id' => 20]);

        DB::table('application_activities_logs')->insert([
            'id' => 1,
            'app_id' => 5,
            'user_id' => 1,
            'type' => 'stage',
            'stage' => 'Lodged',
            'title' => 'Stage moved to Lodged',
            'comment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $item = collect($payload['items'])->firstWhere('key', 'stage:1');

        $this->assertNotNull($item);
        $this->assertSame('Stage', $item['kind']);
        $this->assertSame('STU10', $item['ref']);
        $this->assertSame(10, $item['record_id']);
        $this->assertSame(5, $item['application_id']);
        $this->assertStringNotContainsString('Test College', (string) $item['ref']);
    }

    private function seedBasics(): void
    {
        DB::table('staff')->insert([
            'id' => 1,
            'first_name' => 'A',
            'last_name' => 'B',
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
        DB::table('applications')->insert([
            'id' => 5,
            'client_id' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSchema(): void
    {
        foreach ([
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
    }
}

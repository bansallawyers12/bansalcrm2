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
            'description' => '<p>Test Others. Please ignore</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities_logs')->insert([
            'id' => 30,
            'client_id' => 10,
            'created_by' => 1,
            'subject' => 'added a note',
            'description' => '<span class="text-semi-bold">Others</span><p>Test Others. Please ignore</p>',
            'activity_type' => null,
            'task_status' => 0,
            'pin' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $item = collect($payload['items'])->firstWhere('key', 'note:3');

        $this->assertNotNull($item);
        $this->assertSame('Note', $item['kind']);
        $this->assertSame('Others', $item['title']);
        $this->assertSame('Test Others. Please ignore', $item['body']);
        $this->assertSame(30, $item['activities_log_id']);
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
            'title' => null,
            'comment' => 'moved the stage from  <b>Coe issued</b> to <b>Enrolled</b>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $item = collect($payload['items'])->firstWhere('key', 'stage:1');

        $this->assertNotNull($item);
        $this->assertSame('Stage', $item['kind']);
        $this->assertSame('STU10', $item['ref']);
        $this->assertSame('moved the stage from Coe issued to Enrolled', $item['title']);
        $this->assertSame(10, $item['record_id']);
        $this->assertSame(5, $item['application_id']);
        $this->assertStringNotContainsString('Test College', (string) $item['ref']);
        $this->assertStringNotContainsString('<b>', (string) $item['title']);
    }

    #[Test]
    public function for_staff_client_uploaded_email_shows_as_email_not_document(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 13:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('documents')->insert([
            [
                'id' => 50,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'file_name' => 'uploaded.msg',
                'doc_type' => 'conversion_email_fetch',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 51,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'file_name' => 'passport.pdf',
                'doc_type' => 'documents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 52,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'partner',
                'file_name' => 'partner-mail.msg',
                'doc_type' => 'partner_email_fetch',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('emails')->insert([
            'id' => 7,
            'user_id' => 1,
            'client_id' => 10,
            'type' => 'client',
            'subject' => 'Offer letter',
            'conversion_type' => 'conversion_email_fetch',
            'mail_body_type' => 'inbox',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activities_logs')->insert([
            [
                'id' => 70,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'uploaded Email: Offer letter',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 71,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'uploaded document',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $payload = $this->service->forStaff(1);
        $keys = array_column($payload['items'], 'key');
        $email = collect($payload['items'])->firstWhere('key', 'email:7');

        $this->assertContains('email:7', $keys);
        $this->assertNotNull($email);
        $this->assertSame('Email', $email['kind']);
        $this->assertSame('Offer letter', $email['title']);
        $this->assertSame(70, $email['activities_log_id']);
        $this->assertNotContains('document:50', $keys);
        $this->assertContains('document:51', $keys);
        $this->assertContains('document:52', $keys);
        $doc = collect($payload['items'])->firstWhere('key', 'document:51');
        $this->assertNotNull($doc);
        $this->assertSame(71, $doc['activities_log_id']);
    }

    #[Test]
    public function for_staff_skips_checklist_placeholder_documents_without_file_name(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('documents')->insert([
            [
                'id' => 60,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'file_name' => null,
                'doc_type' => 'documents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 61,
                'user_id' => 1,
                'client_id' => 10,
                'type' => 'client',
                'file_name' => 'Form956.pdf',
                'doc_type' => 'documents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('activities_logs')->insert([
            'id' => 80,
            'client_id' => 10,
            'created_by' => 1,
            'subject' => 'added document checklist',
            'description' => '',
            'activity_type' => null,
            'task_status' => 0,
            'pin' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $keys = array_column($payload['items'], 'key');

        $this->assertNotContains('document:60', $keys);
        $this->assertContains('document:61', $keys);
        $this->assertContains('feed:80', $keys);
        $checklist = collect($payload['items'])->firstWhere('key', 'feed:80');
        $this->assertSame('Checklist', $checklist['kind']);
    }

    #[Test]
    public function for_staff_includes_receipt_not_used_action_assign_checklist_and_service_feed_rows(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne'));
        $this->seedBasics();

        DB::table('activities_logs')->insert([
            [
                'id' => 201,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'documents document moved to Not Used Tab',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 202,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'added client receipt with Receipt Id-9 and Trans. No-Rec9',
                'description' => '',
                'activity_type' => 'receipt_created',
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 203,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'added student invoice with invoice No-INV1',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 204,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'set action for Test Staff',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 205,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'added an interested service',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 206,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'added document checklist',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 207,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'uploaded document',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 208,
                'client_id' => 10,
                'created_by' => 1,
                'subject' => 'A updated client profile details',
                'description' => '',
                'activity_type' => null,
                'task_status' => 0,
                'pin' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('application_activities_logs')->insert([
            'id' => 11,
            'app_id' => 5,
            'user_id' => 1,
            'type' => 'document',
            'stage' => 'Lodged',
            'title' => null,
            'comment' => 'added a document',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = $this->service->forStaff(1);
        $byKey = collect($payload['items'])->keyBy('key');

        $this->assertSame('Document', $byKey['feed:201']['kind']);
        $this->assertSame('Receipt', $byKey['feed:202']['kind']);
        $this->assertSame('Invoice', $byKey['feed:203']['kind']);
        $this->assertSame('Action assigned', $byKey['feed:204']['kind']);
        $this->assertSame('Service', $byKey['feed:205']['kind']);
        $this->assertSame('Checklist', $byKey['feed:206']['kind']);
        $this->assertArrayNotHasKey('feed:207', $byKey->all());
        $this->assertSame('Profile', $byKey['feed:208']['kind']);
        $this->assertSame('Document', $byKey['appdoc:11']['kind']);
        $this->assertSame(5, $byKey['appdoc:11']['application_id']);
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
            $table->text('description')->nullable();
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
            $table->string('doc_type')->nullable();
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

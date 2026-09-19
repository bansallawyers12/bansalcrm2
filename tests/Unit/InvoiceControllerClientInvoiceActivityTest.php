<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\InvoiceController;
use App\Models\ActivitiesLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class InvoiceControllerClientInvoiceActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('activities_logs');
        Schema::create('activities_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('activity_type')->nullable();
            $table->integer('task_status')->default(0);
            $table->integer('pin')->default(0);
            $table->timestamps();
        });
    }

    #[Test]
    public function log_client_invoice_activity_writes_student_invoice_subject(): void
    {
        Auth::shouldReceive('user')->andReturn((object) ['id' => 7]);

        $controller = new InvoiceController;
        $method = new ReflectionMethod(InvoiceController::class, 'logClientInvoiceActivity');
        $method->invoke($controller, 10, '2026/09/99');

        $log = ActivitiesLog::query()->first();
        $this->assertNotNull($log);
        $this->assertSame(10, (int) $log->client_id);
        $this->assertSame(7, (int) $log->created_by);
        $this->assertSame('added student invoice with invoice No-2026/09/99', $log->subject);
    }

    #[Test]
    public function log_client_invoice_activity_skips_invalid_client_or_invoice_no(): void
    {
        Auth::shouldReceive('user')->never();

        $controller = new InvoiceController;
        $method = new ReflectionMethod(InvoiceController::class, 'logClientInvoiceActivity');
        $method->invoke($controller, 0, '2026/09/99');
        $method->invoke($controller, 10, '');

        $this->assertSame(0, ActivitiesLog::query()->count());
    }
}

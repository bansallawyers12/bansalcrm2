<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->foreign('application_id')->references('id')->on('applications')->nullOnDelete();
            $table->foreign('activities_log_id')->references('id')->on('activities_logs')->nullOnDelete();

            $table->unique(
                ['staff_id', 'record_type', 'record_id', 'application_key', 'session_date'],
                'staff_file_sessions_staff_record_day'
            );
            $table->index(['staff_id', 'session_date']);
            $table->index('last_heartbeat_at');
            $table->unique('activities_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_file_sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Followup consultants + calendar settings + block timings (replaces removed appointment_* migrations).
     */
    public function up(): void
    {
        if (! Schema::hasTable('followup_consultants')) {
            Schema::create('followup_consultants', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->comment('Stable key, e.g. calendar route segment');
                $table->string('name')->comment('Display label');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('status')->default(1)->comment('1 = active, 0 = inactive');
                $table->timestamps();

                $table->unique('slug');
                $table->unique('name');
                $table->index(['status', 'sort_order']);
            });
        }

        if (Schema::hasTable('followup_consultants') && DB::table('followup_consultants')->count() === 0) {
            $now = now();
            DB::table('followup_consultants')->insert([
                ['slug' => 'ankit', 'name' => 'Ankit Calendar', 'sort_order' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['slug' => 'rakshita', 'name' => 'Rakshita Calendar', 'sort_order' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['slug' => 'jaspreet', 'name' => 'Jaspreet Calendar', 'sort_order' => 2, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['slug' => 'syed', 'name' => 'Syed Calendar', 'sort_order' => 3, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (! Schema::hasTable('followup_calendar_settings')) {
            Schema::create('followup_calendar_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('followup_consultant_id')
                    ->constrained('followup_consultants')
                    ->cascadeOnDelete();
                $table->string('service_type', 32)->default('free')->comment('free, paid, …');
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedSmallInteger('slot_duration_minutes')->default(15);
                $table->json('available_days')->comment('PHP date N: 1=Mon … 7=Sun; [] = all days');
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['followup_consultant_id', 'service_type']);
                $table->index(['service_type', 'is_active']);
            });
        }

        if (! Schema::hasTable('followup_calendar_block_timings')) {
            Schema::create('followup_calendar_block_timings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('block_date');
                $table->boolean('is_all_day')->default(false);
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('block_type', 32)->comment('unavailable, busy, …');
                $table->string('recurrence', 32)->default('none')->comment('none, daily, weekly, monthly');
                $table->json('locations')->nullable()->comment('Office keys, empty = all');
                $table->json('calendar_types')->nullable()->comment('Calendar / service keys, empty = all');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['block_date', 'is_active']);
                $table->index('block_type');
            });
        }
    }

    /**
     * Reverse the migrations — drops tables only if present (destructive).
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_calendar_settings');
        Schema::dropIfExists('followup_calendar_block_timings');
        Schema::dropIfExists('followup_consultants');
    }
};

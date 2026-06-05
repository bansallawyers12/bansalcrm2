<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align free-consultation calendar windows to 10:00–17:00 (was 10:45–17:00).
     */
    public function up(): void
    {
        if (! Schema::hasTable('followup_calendar_settings')) {
            return;
        }

        DB::table('followup_calendar_settings')
            ->where('start_time', '10:45:00')
            ->update([
                'start_time' => '10:00:00',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Not reversed: cannot distinguish rows migrated from 10:45 from newly created 10:00 rows.
    }
};

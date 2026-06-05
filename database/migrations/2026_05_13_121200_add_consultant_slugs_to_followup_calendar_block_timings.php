<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('followup_calendar_block_timings')) {
            return;
        }

        if (! Schema::hasColumn('followup_calendar_block_timings', 'consultant_slugs')) {
            Schema::table('followup_calendar_block_timings', function (Blueprint $table) {
                $table->json('consultant_slugs')->nullable()->after('calendar_types')->comment('Follow-up consultant slugs; empty or null = all');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('followup_calendar_block_timings')) {
            return;
        }

        if (Schema::hasColumn('followup_calendar_block_timings', 'consultant_slugs')) {
            Schema::table('followup_calendar_block_timings', function (Blueprint $table) {
                $table->dropColumn('consultant_slugs');
            });
        }
    }
};

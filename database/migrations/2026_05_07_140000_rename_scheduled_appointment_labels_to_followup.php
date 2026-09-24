<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notes')) {
            if (Schema::hasColumn('notes', 'task_group')) {
                DB::table('notes')->where('task_group', 'Appointment')->update(['task_group' => 'Followup']);
            }
            if (Schema::hasColumn('notes', 'title')) {
                foreach (DB::table('notes')->where('title', 'like', 'Appointment —%')->cursor() as $row) {
                    DB::table('notes')->where('id', $row->id)->update([
                        'title' => str_replace('Appointment —', 'Followup —', (string) $row->title),
                    ]);
                }
            }
        }

        if (Schema::hasTable('activities_logs')) {
            if (Schema::hasColumn('activities_logs', 'task_group')) {
                DB::table('activities_logs')->where('task_group', 'Appointment')->update(['task_group' => 'Followup']);
            }
            if (Schema::hasColumn('activities_logs', 'subject')) {
                foreach (DB::table('activities_logs')->where('subject', 'like', 'Scheduled appointment (%')->cursor() as $row) {
                    DB::table('activities_logs')->where('id', $row->id)->update([
                        'subject' => str_replace('Scheduled appointment (', 'Scheduled follow-up (', (string) $row->subject),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Data migration is not safely reversible without losing unrelated Followup rows.
    }
};

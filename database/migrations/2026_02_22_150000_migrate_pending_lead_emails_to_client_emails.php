<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Catch-up: Migrates emails for admins with is_lead_migrate_to_admin=1 and is_email_migrate=0
     * that were missed (e.g. got the lead flag after the original migration ran).
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_emails')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'email') || !Schema::hasColumn('admins', 'email_type') || !Schema::hasColumn('admins', 'is_email_migrate')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->where('is_email_migrate', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email', 'email_type')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $userExists = DB::table('admins')->where('id', 1)->exists();
        if (!$userExists) {
            return;
        }

        $now = now();

        foreach ($admins as $row) {
            try {
                $existing = DB::table('client_emails')
                    ->where('client_id', $row->id)
                    ->where('client_email', $row->email)
                    ->first();

                if ($existing) {
                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_email_migrate' => 1, 'updated_at' => $now]);
                } else {
                    DB::table('client_emails')->insert([
                        'user_id' => 1,
                        'client_id' => $row->id,
                        'email_type' => $row->email_type,
                        'client_email' => $row->email,
                        'is_verified' => null,
                        'verified_at' => null,
                        'verified_by' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_email_migrate' => 1, 'updated_at' => $now]);
                }
            } catch (\Throwable $e) {
                DB::table('admins')
                    ->where('id', $row->id)
                    ->update(['is_email_migrate' => 2, 'updated_at' => $now]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: cannot reliably undo; migrated data stays in client_emails
    }
};

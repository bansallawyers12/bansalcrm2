<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1. Adds admins.is_email_migrate: 0=default, 1=success, 2=fail
     * 2. Migrates unique emails from admins (is_lead_migrate_to_admin=1) to client_emails.
     *    Only inserts when (client_id, client_email) does not already exist.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_emails')) {
            return;
        }

        // Step 1: Add is_email_migrate column to admins table
        if (!Schema::hasColumn('admins', 'is_email_migrate')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->tinyInteger('is_email_migrate')->default(0)->after('is_lead_migrate_to_admin')
                    ->comment('0=default, 1=migrated to client_emails success, 2=fail');
            });
        }

        // Step 2: Migrate unique emails from admins (is_lead_migrate_to_admin=1) to client_emails
        if (!Schema::hasColumn('admins', 'email') || !Schema::hasColumn('admins', 'email_type')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email', 'email_type')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        // Ensure user_id=1 exists for client_emails
        $userExists = DB::table('admins')->where('id', 1)->exists();
        if (!$userExists) {
            return;
        }

        $now = now();

        foreach ($admins as $row) {
            try {
                // Check if (client_id, client_email) already exists - only insert unique
                $existing = DB::table('client_emails')
                    ->where('client_id', $row->id)
                    ->where('client_email', $row->email)
                    ->first();

                if ($existing) {
                    // Already exists - treat as success (no duplicate insert)
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
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_email_migrate')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('is_email_migrate');
            });
        }

        // Note: Migrated rows in client_emails are not removed on down() -
        // reversing would require tracking which rows were inserted by this migration.
    }
};

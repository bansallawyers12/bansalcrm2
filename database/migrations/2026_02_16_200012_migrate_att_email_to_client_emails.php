<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrates att_email from admins (role=7 clients) to client_emails.
     * - Inserts into client_emails with user_id=1, email_type='Secondary'
     * - Updates admins.att_email to null only after successful insert
     * - On insert failure, skips update - no data loss (att_email preserved)
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_emails')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'att_email')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('role', 7)
            ->where(function ($q) {
                $q->where('att_email', '!=', '')
                    ->whereNotNull('att_email');
            })
            ->select('id', 'att_email')
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
                DB::table('client_emails')->insert([
                    'user_id' => 1,
                    'client_id' => $row->id,
                    'email_type' => 'Secondary',
                    'client_email' => $row->att_email,
                    'is_verified' => null,
                    'verified_at' => null,
                    'verified_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('admins')
                    ->where('id', $row->id)
                    ->update(['att_email' => null]);
            } catch (\Exception $e) {
                // Insert failed - do not update admins, keep original att_email
            }
        }
    }

    /**
     * Reverse the migrations.
     * No-op: cannot restore att_email values; migrated data remains in client_emails.
     */
    public function down(): void
    {
        // No-op - data stays in client_emails
    }
};

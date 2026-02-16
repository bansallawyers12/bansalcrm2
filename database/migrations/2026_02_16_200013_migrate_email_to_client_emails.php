<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Copies email and email_type from admins (role=7 clients) to client_emails.
     * - Does NOT update or null admins.email or admins.email_type
     * - If client_id + client_email exists (e.g. from att_email as Secondary): UPDATE email_type to match admins
     * - If not exists: INSERT with email_type from admins as-is (null stays null)
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_emails')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'email') || !Schema::hasColumn('admins', 'email_type')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('role', 7)
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
            $existing = DB::table('client_emails')
                ->where('client_id', $row->id)
                ->where('client_email', $row->email)
                ->first();

            if ($existing) {
                DB::table('client_emails')
                    ->where('id', $existing->id)
                    ->update([
                        'email_type' => $row->email_type,
                        'updated_at' => $now,
                    ]);
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
            }
        }
    }

    /**
     * Reverse the migrations.
     * No-op: admins was never updated; client_emails rows remain.
     */
    public function down(): void
    {
        // No-op
    }
};

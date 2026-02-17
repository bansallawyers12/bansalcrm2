<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrates att_country_code and att_phone from admins (role=7 clients) to client_phones.
     * - Inserts into client_phones with user_id=1, contact_type='Secondary'
     * - Updates admins.att_country_code and att_phone to null only after successful insert
     * - On insert failure, skips update - no data loss
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_phones')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'att_country_code') || !Schema::hasColumn('admins', 'att_phone')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('role', 7)
            ->where(function ($q) {
                $q->where('att_phone', '!=', '')
                    ->whereNotNull('att_phone');
            })
            ->select('id', 'att_country_code', 'att_phone')
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
                DB::table('client_phones')->insert([
                    'user_id' => 1,
                    'client_id' => $row->id,
                    'contact_type' => 'Secondary',
                    'client_country_code' => $row->att_country_code,
                    'client_phone' => $row->att_phone,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('admins')
                    ->where('id', $row->id)
                    ->update([
                        'att_country_code' => null,
                        'att_phone' => null,
                    ]);
            } catch (\Exception $e) {
                // Insert failed - do not update admins, keep original data
            }
        }
    }

    /**
     * Reverse the migrations.
     * No-op: cannot restore att_* values; migrated data remains in client_phones.
     */
    public function down(): void
    {
        // No-op - data stays in client_phones
    }
};

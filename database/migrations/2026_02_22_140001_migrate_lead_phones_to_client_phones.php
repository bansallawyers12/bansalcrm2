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
     * 1. Adds admins.is_phone_migrate: 0=default, 1=success, 2=fail
     * 2. Migrates unique phones from admins (is_lead_migrate_to_admin=1) to client_phones.
     *    Only inserts when (client_id, client_phone) does not already exist.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_phones')) {
            return;
        }

        // Step 1: Add is_phone_migrate column to admins table
        if (!Schema::hasColumn('admins', 'is_phone_migrate')) {
            $afterCol = Schema::hasColumn('admins', 'is_email_migrate') ? 'is_email_migrate' : 'is_lead_migrate_to_admin';
            Schema::table('admins', function (Blueprint $table) use ($afterCol) {
                $table->tinyInteger('is_phone_migrate')->default(0)->after($afterCol)
                    ->comment('0=default, 1=migrated to client_phones success, 2=fail');
            });
        }

        // Step 2: Migrate unique phones from admins (is_lead_migrate_to_admin=1) to client_phones
        if (!Schema::hasColumn('admins', 'phone') || !Schema::hasColumn('admins', 'contact_type')) {
            return;
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'phone', 'country_code', 'contact_type')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        // Ensure user_id=1 exists for client_phones
        $userExists = DB::table('admins')->where('id', 1)->exists();
        if (!$userExists) {
            return;
        }

        $now = now();

        foreach ($admins as $row) {
            try {
                // Check if (client_id, client_phone) already exists - only insert unique
                $existing = DB::table('client_phones')
                    ->where('client_id', $row->id)
                    ->where('client_phone', $row->phone)
                    ->first();

                if ($existing) {
                    // Already exists - treat as success (no duplicate insert)
                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_phone_migrate' => 1, 'updated_at' => $now]);
                } else {
                    $insertData = [
                        'user_id' => 1,
                        'client_id' => $row->id,
                        'contact_type' => $row->contact_type,
                        'client_phone' => $row->phone,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (Schema::hasColumn('client_phones', 'client_country_code')) {
                        $insertData['client_country_code'] = $row->country_code;
                    }

                    if (Schema::hasColumn('client_phones', 'is_verified')) {
                        $insertData['is_verified'] = false;
                    }

                    DB::table('client_phones')->insert($insertData);

                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_phone_migrate' => 1, 'updated_at' => $now]);
                }
            } catch (\Throwable $e) {
                DB::table('admins')
                    ->where('id', $row->id)
                    ->update(['is_phone_migrate' => 2, 'updated_at' => $now]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_phone_migrate')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('is_phone_migrate');
            });
        }

        // Note: Migrated rows in client_phones are not removed on down() -
        // reversing would require tracking which rows were inserted by this migration.
    }
};

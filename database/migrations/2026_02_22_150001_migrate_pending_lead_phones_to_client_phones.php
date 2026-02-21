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
     * Catch-up: Migrates phones for admins with is_lead_migrate_to_admin=1 and is_phone_migrate=0
     * that were missed (e.g. got the lead flag after the original migration ran).
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasTable('client_phones')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'phone') || !Schema::hasColumn('admins', 'contact_type')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'is_phone_migrate')) {
            $afterCol = Schema::hasColumn('admins', 'is_email_migrate') ? 'is_email_migrate' : 'is_lead_migrate_to_admin';
            Schema::table('admins', function (Blueprint $table) use ($afterCol) {
                $table->tinyInteger('is_phone_migrate')->default(0)->after($afterCol)
                    ->comment('0=default, 1=migrated to client_phones success, 2=fail');
            });
        }

        $admins = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->where('is_phone_migrate', 0)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'phone', 'country_code', 'contact_type')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $userId = DB::table('admins')->where('id', 1)->value('id')
            ?? DB::table('admins')->min('id');
        if ($userId === null) {
            return;
        }

        $now = now();

        foreach ($admins as $row) {
            try {
                $existing = DB::table('client_phones')
                    ->where('client_id', $row->id)
                    ->where('client_phone', $row->phone)
                    ->first();

                if ($existing) {
                    DB::table('admins')
                        ->where('id', $row->id)
                        ->update(['is_phone_migrate' => 1, 'updated_at' => $now]);
                } else {
                    $insertData = [
                        'user_id' => $userId,
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
        // No-op: cannot reliably undo; migrated data stays in client_phones
    }
};

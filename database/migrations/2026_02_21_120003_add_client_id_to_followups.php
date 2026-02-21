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
     * Adds client_id to followups (admins.id) and populates from leads.client_id.
     * Must run AFTER migrate_leads_to_admins (which sets leads.client_id).
     *
     * Simple: followups.client_id = leads.client_id where followups.lead_id = leads.id
     */
    public function up(): void
    {
        if (!Schema::hasTable('followups')) {
            return;
        }

        if (!Schema::hasColumn('followups', 'client_id')) {
            Schema::table('followups', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('lead_id')
                    ->comment('FK to admins.id - from leads.client_id');
            });
        }

        if (!Schema::hasTable('leads') || !Schema::hasColumn('leads', 'client_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('
                UPDATE followups f
                SET client_id = l.client_id
                FROM leads l
                WHERE f.lead_id = l.id
                  AND l.client_id IS NOT NULL
            ');
        } else {
            DB::statement('
                UPDATE followups f
                INNER JOIN leads l ON f.lead_id = l.id
                SET f.client_id = l.client_id
                WHERE l.client_id IS NOT NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('followups')) {
            return;
        }

        if (Schema::hasColumn('followups', 'client_id')) {
            Schema::table('followups', function (Blueprint $table) {
                $table->dropColumn('client_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds migration tracking columns:
     * - admins.is_lead_migrate_to_admin: 0=default, 1=successfully migrated from lead
     * - leads.is_migrate: 0=pending, 1=success, 2=fail, 3=already exists
     */
    public function up(): void
    {
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'is_lead_migrate_to_admin')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->tinyInteger('is_lead_migrate_to_admin')->default(0)->after('lead_id')
                    ->comment('0=default, 1=migrated from lead');
            });
        }

        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'is_migrate')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->tinyInteger('is_migrate')->default(0)
                    ->comment('0=pending, 1=success, 2=fail, 3=already exists');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_lead_migrate_to_admin')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('is_lead_migrate_to_admin');
            });
        }

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'is_migrate')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('is_migrate');
            });
        }
    }
};

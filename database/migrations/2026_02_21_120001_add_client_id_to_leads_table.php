<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds client_id to leads table. Populated by MigrateLeadsToAdminsCommand
     * when lead is migrated to admins (leads.client_id = admins.id).
     * Used to simplify followups.client_id population.
     */
    public function up(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        if (!Schema::hasColumn('leads', 'client_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()
                    ->comment('FK to admins.id - set when lead migrated to admins');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'client_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('client_id');
            });
        }
    }
};

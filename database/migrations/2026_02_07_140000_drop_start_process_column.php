<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove start_process column (not used in migrationmanager2).
     */
    public function up(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'start_process')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('start_process');
            });
        }

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'start_process')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('start_process');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'start_process')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('start_process')->nullable();
            });
        }

        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'start_process')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('start_process')->nullable();
            });
        }
    }
};

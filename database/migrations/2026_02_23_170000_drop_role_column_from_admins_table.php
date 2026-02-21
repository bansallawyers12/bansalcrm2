<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Staff has been migrated out of admins table; admins now holds clients/leads only.
     * Role column is no longer needed.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasColumn('admins', 'role')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'role')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->integer('role')->nullable()->after('id');
            });
        }
    }
};

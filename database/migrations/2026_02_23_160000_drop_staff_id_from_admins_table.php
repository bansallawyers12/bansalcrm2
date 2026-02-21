<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Removes staff_id column from admins table (no longer needed).
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        if (Schema::hasColumn('admins', 'staff_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('staff_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        if (!Schema::hasColumn('admins', 'staff_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('client_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop level column from staff_login_logs.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('staff_login_logs') && Schema::hasColumn('staff_login_logs', 'level')) {
            Schema::table('staff_login_logs', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('staff_login_logs') && !Schema::hasColumn('staff_login_logs', 'level')) {
            Schema::table('staff_login_logs', function (Blueprint $table) {
                $table->string('level', 50)->nullable()->after('id');
            });
        }
    }
};

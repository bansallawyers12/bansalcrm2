<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename user_logs table to staff_login_logs.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('user_logs') && !Schema::hasTable('staff_login_logs')) {
            Schema::rename('user_logs', 'staff_login_logs');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('staff_login_logs') && !Schema::hasTable('user_logs')) {
            Schema::rename('staff_login_logs', 'user_logs');
        }
    }
};

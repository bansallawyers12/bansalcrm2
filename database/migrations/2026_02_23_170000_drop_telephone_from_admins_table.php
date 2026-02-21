<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops telephone column from admins table (unused; phone + country_code are used instead).
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        if (Schema::hasColumn('admins', 'telephone')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('telephone');
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

        if (!Schema::hasColumn('admins', 'telephone')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('telephone', 100)->nullable()->after('country_code');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops redundant contact columns from staff:
     * - telephone (use phone + country_code)
     * - att_email, att_country_code, att_phone (alternate contact - removed)
     */
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        $columnsToDrop = ['telephone', 'att_email', 'att_country_code', 'att_phone'];
        $existing = array_filter($columnsToDrop, fn ($c) => Schema::hasColumn('staff', $c));

        if (!empty($existing)) {
            Schema::table('staff', function (Blueprint $table) use ($existing) {
                $table->dropColumn(array_values($existing));
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'telephone')) {
                $table->string('telephone', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('staff', 'att_email')) {
                $table->string('att_email')->nullable()->after('telephone');
            }
            if (!Schema::hasColumn('staff', 'att_country_code')) {
                $table->string('att_country_code', 10)->nullable()->after('att_email');
            }
            if (!Schema::hasColumn('staff', 'att_phone')) {
                $table->string('att_phone', 50)->nullable()->after('att_country_code');
            }
        });
    }
};

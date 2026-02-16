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
     *
     * Note: att_email, att_country_code, att_phone are NOT dropped - kept for
     * alternate contact until data is migrated to client_alternate_contacts.
     */
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        $columnsToDrop = ['telephone'];
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
        });
    }
};

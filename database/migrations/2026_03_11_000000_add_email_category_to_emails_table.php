<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds email_category to emails for Client/College sub-tabs on client detail only.
     * Nullable: existing rows remain NULL and are shown in Client sub-tab (no data change, no loss).
     */
    public function up(): void
    {
        if (!Schema::hasTable('emails')) {
            return;
        }

        if (Schema::hasColumn('emails', 'email_category')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            $table->string('email_category', 50)->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('emails') || !Schema::hasColumn('emails', 'email_category')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('email_category');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Removes client_matter_id from mail_reports - matter ID not used in this CRM.
     */
    public function up(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            if (Schema::hasColumn('mail_reports', 'client_matter_id')) {
                $table->dropColumn('client_matter_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('mail_reports', 'client_matter_id')) {
                $table->unsignedBigInteger('client_matter_id')->nullable()->after('client_id');
            }
        });
    }
};

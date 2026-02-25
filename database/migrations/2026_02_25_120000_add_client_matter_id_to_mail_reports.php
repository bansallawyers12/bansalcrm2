<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds client_matter_id to mail_reports for matter-scoped email archival (S3/Email tab).
     */
    public function up(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('mail_reports', 'client_matter_id')) {
                $table->unsignedBigInteger('client_matter_id')->nullable()->after('client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            if (Schema::hasColumn('mail_reports', 'client_matter_id')) {
                $table->dropColumn('client_matter_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for partner detail Applications / Student tab queries.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['partner_id', 'stage', 'overall_status'], 'applications_partner_stage_status_idx');
            $table->index(['partner_id', 'created_at'], 'applications_partner_created_at_idx');
        });

        Schema::table('application_fee_options', function (Blueprint $table) {
            $table->index(['app_id', 'id'], 'application_fee_options_app_id_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('applications_partner_stage_status_idx');
            $table->dropIndex('applications_partner_created_at_idx');
        });

        Schema::table('application_fee_options', function (Blueprint $table) {
            $table->dropIndex('application_fee_options_app_id_id_idx');
        });
    }
};

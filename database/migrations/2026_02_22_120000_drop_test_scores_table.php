<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops the legacy test_scores table. Data has been migrated to client_testscore.
     * All active flows use ClientTestScore / client_testscore.
     */
    public function up(): void
    {
        Schema::dropIfExists('test_scores');
    }

    /**
     * Reverse the migrations.
     * Note: Cannot recreate - use create_test_scores_table migration if restoration needed.
     */
    public function down(): void
    {
        // No-op: table structure would need to be recreated via original migration
    }
};

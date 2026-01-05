<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops the education table which stored educational background records.
     * This table is no longer used in the application.
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('education');
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drop without full schema definition.
     * If restoration is needed, use a database backup.
     *
     * @return void
     */
    public function down(): void
    {
        // Cannot restore without full schema definition
        // If restoration is needed, use a database backup
    }
};


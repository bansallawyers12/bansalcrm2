<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops the education and subject_areas tables which only exist in PostgreSQL.
     * These tables are not present in MySQL and should be removed for consistency.
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('education');
        Schema::dropIfExists('subject_areas');
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

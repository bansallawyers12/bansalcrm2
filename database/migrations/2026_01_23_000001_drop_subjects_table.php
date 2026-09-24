<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops the subjects table as it's no longer used in the CRM.
     * Subject functionality has been removed from:
     * - Products (subject dropdown was never saving data)
     * - Partners (subject dropdown was never saving data)
     * - Admin Console (SubjectController and views removed)
     */
    public function up(): void
    {
        Schema::dropIfExists('subjects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate subjects table if needed (basic structure)
        Schema::create('subjects', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('subject_area')->nullable();
            $table->timestamps();
        });
    }
};

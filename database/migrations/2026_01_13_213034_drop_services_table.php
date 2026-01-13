<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the services table - unused feature, no longer needed
        // The Services module was removed from the application
        // Note: This is NOT the interested_services table which is still actively used
        Schema::dropIfExists('services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot recreate table structure without full schema definition
        // The services table structure was:
        // - id (primary key)
        // - title (varchar)
        // - parent (int) - for hierarchical tree structure
        // - description (text)
        // - services_icon (varchar)
        // - services_image (varchar)
        // - status (tinyint)
        // - created_at, updated_at (timestamps)
        
        // If restoration is needed, restore from database backup
    }
};
